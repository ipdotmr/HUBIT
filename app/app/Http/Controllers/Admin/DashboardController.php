<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_services' => Service::where('status', 'active')->count(),
            'unpaid_invoices' => Invoice::where('status', 'unpaid')->count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'pending_cancellations' => Service::where('status', 'pending_cancellation')->count(),
            'pending_module_actions' => 0, // This would come from a module queue table
            'total_clients' => Client::count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
        ];

        $recentOrders = Order::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'client_name' => $order->client->name ?? 'N/A',
                    'amount' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        $recentInvoices = Invoice::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'client_name' => $invoice->client->name ?? 'N/A',
                    'amount' => $invoice->total,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                ];
            });

        $recentTickets = Ticket::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'client_name' => $ticket->client->name ?? 'N/A',
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at->format('Y-m-d H:i'),
                ];
            });

        $incomeData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $income = Invoice::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');
            
            $incomeData[] = [
                'month' => $month->format('M Y'),
                'income' => (float) $income,
            ];
        }

        $clientActivity = Client::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'created_at' => $client->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentInvoices' => $recentInvoices,
            'recentTickets' => $recentTickets,
            'incomeData' => $incomeData,
            'clientActivity' => $clientActivity,
        ]);
    }
}
