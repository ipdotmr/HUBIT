<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function services(Request $request): Response
    {
        $cacheKey = 'reports:services:'.md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Service::with(['product', 'client']);

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('provisioner')) {
                $query->where('provisioner', $request->input('provisioner'));
            }

            $services = $query->get();

            return [
                'total_services' => $services->count(),
                'active_services' => $services->where('status', 'active')->count(),
                'suspended_services' => $services->where('status', 'suspended')->count(),
                'pending_services' => $services->whereIn('status', ['pending', 'pending_suspension', 'pending_unsuspension'])->count(),
                'by_provisioner' => $services->groupBy('provisioner')->map->count(),
                'by_product' => $services->groupBy('product.name')->map->count(),
                'recent_services' => $services->sortByDesc('created_at')->take(10)->values(),
                'mrr' => $services->where('status', 'active')->sum('recurring_amount'),
            ];
        });

        return Inertia::render('Admin/Reports/Services', [
            'data' => $data,
            'filters' => $request->only(['status', 'provisioner']),
        ]);
    }

    public function wallet(Request $request): Response
    {
        $cacheKey = 'reports:wallet:'.md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = WalletTransaction::with(['wallet.client']);

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->input('date_to'));
            }

            $transactions = $query->get();

            return [
                'total_transactions' => $transactions->count(),
                'total_credits' => $transactions->where('type', 'credit')->sum('amount'),
                'total_debits' => $transactions->where('type', 'debit')->sum('amount'),
                'by_type' => $transactions->groupBy('type')->map->count(),
                'recent_transactions' => $transactions->sortByDesc('created_at')->take(20)->values(),
                'daily_volume' => $transactions->groupBy(fn ($t) => $t->created_at->format('Y-m-d'))
                    ->map(fn ($group) => $group->sum('amount'))
                    ->sortKeys(),
            ];
        });

        return Inertia::render('Admin/Reports/Wallet', [
            'data' => $data,
            'filters' => $request->only(['type', 'date_from', 'date_to']),
        ]);
    }

    public function exportServices(Request $request)
    {
        $services = Service::with(['product', 'client'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('provisioner'), fn ($q) => $q->where('provisioner', $request->input('provisioner')))
            ->get();

        $csv = "ID,Client,Product,Status,Provisioner,Recurring Amount,Next Due,Created\n";

        foreach ($services as $service) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s\n",
                $service->id,
                $service->client->company_name ?? $service->client->contact_name,
                $service->product->name ?? 'N/A',
                $service->status,
                $service->provisioner ?? 'N/A',
                $service->recurring_amount ?? '0.00',
                $service->next_due_at?->format('Y-m-d') ?? 'N/A',
                $service->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="services_'.date('Y-m-d').'.csv"',
        ]);
    }

    public function exportWallet(Request $request)
    {
        $transactions = WalletTransaction::with(['wallet.client'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('date_from'), fn ($q) => $q->where('created_at', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->where('created_at', '<=', $request->input('date_to')))
            ->get();

        $csv = "ID,Client,Type,Amount,Balance After,Description,Created\n";

        foreach ($transactions as $txn) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s\n",
                $txn->id,
                $txn->wallet->client->company_name ?? $txn->wallet->client->contact_name,
                $txn->type,
                $txn->amount,
                $txn->balance_after ?? '0.00',
                '"'.str_replace('"', '""', $txn->description ?? '').'"',
                $txn->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wallet_transactions_'.date('Y-m-d').'.csv"',
        ]);
    }
}
