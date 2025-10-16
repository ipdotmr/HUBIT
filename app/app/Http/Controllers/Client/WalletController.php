<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\AuditLogService;
use App\Services\Wallet\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request): Response
    {
        $client = Auth::user()->client;
        $wallet = $client->wallet ?? Wallet::create(['client_id' => $client->id]);

        $query = WalletTransaction::where('wallet_id', $wallet->id);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->input('date_to'));
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Client/Wallet/Index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'filters' => $request->only(['type', 'date_from', 'date_to']),
        ]);
    }

    public function addCredit(Request $request): RedirectResponse
    {
        $client = Auth::user()->client;
        $wallet = $client->wallet ?? Wallet::create(['client_id' => $client->id]);

        Gate::authorize('update', $wallet);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:10000'],
            'payment_method' => ['required', 'in:stripe,paypal,offline'],
        ]);

        $invoice = $this->walletService->createTopUpInvoice(
            $wallet,
            $request->input('amount'),
            $request->input('payment_method')
        );

        $this->auditLog->log('wallet.topup_initiated', $wallet, [
            'amount' => $request->input('amount'),
            'payment_method' => $request->input('payment_method'),
            'invoice_id' => $invoice->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Top-up invoice created successfully');
    }

    public function transactions(Request $request): Response
    {
        $client = Auth::user()->client;
        $wallet = $client->wallet;

        if (! $wallet) {
            return redirect()->route('wallet.index');
        }

        Gate::authorize('view', $wallet);

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->with(['invoice'])
            ->latest()
            ->paginate(50);

        return Inertia::render('Client/Wallet/Transactions', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}
