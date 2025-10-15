<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PaymentTransaction::with(['invoice', 'client', 'paymentAccount', 'reviewer'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->method) {
            $query->where('method', $request->method);
        }

        $transactions = $query->paginate(20);

        return \Inertia\Inertia::render('Admin/Billing/Transactions', [
            'transactions' => $transactions,
            'filters' => $request->only(['status', 'method', 'currency']),
        ]);
    }

    public function show(\App\Models\PaymentTransaction $transaction)
    {
        $transaction->load(['invoice', 'client', 'paymentAccount', 'reviewer']);

        return response()->json($transaction);
    }

    public function review(Request $request, \App\Models\PaymentTransaction $transaction)
    {
        $validated = $request->validate([
            'approve' => 'required|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        $service = app(\App\Services\Billing\OfflinePaymentService::class);
        $result = $service->review(
            $transaction->id,
            $validated['approve'],
            $validated['note'] ?? ''
        );

        return redirect()->back()->with('success', $result['message']);
    }
}
