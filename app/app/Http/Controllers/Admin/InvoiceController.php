<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('client');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'client_name' => $invoice->client ? $invoice->client->name : 'N/A',
                    'date' => $invoice->created_at->format('Y-m-d'),
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
                    'total' => number_format($invoice->total, 2),
                    'status' => $invoice->status,
                ];
            });

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $clients = Client::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                ];
            });

        return Inertia::render('Admin/Invoices/Create', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $total = collect($validated['items'])->sum('amount');

        $invoice = Invoice::create([
            'client_id' => $validated['client_id'],
            'due_date' => $validated['due_date'],
            'total' => $total,
            'status' => 'unpaid',
            'items' => $validated['items'],
        ]);

        return redirect()->route('managit.invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client');

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'client_name' => $invoice->client ? $invoice->client->name : 'N/A',
                'client_email' => $invoice->client ? $invoice->client->email : 'N/A',
                'date' => $invoice->created_at->format('Y-m-d'),
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
                'total' => number_format($invoice->total, 2),
                'status' => $invoice->status,
                'items' => $invoice->items ?? [],
            ],
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                ];
            });

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => [
                'id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '',
                'status' => $invoice->status,
                'items' => $invoice->items ?? [],
            ],
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid,cancelled,refunded',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        $total = collect($validated['items'])->sum('amount');

        $invoice->update([
            'client_id' => $validated['client_id'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'total' => $total,
            'items' => $validated['items'],
        ]);

        return redirect()->route('managit.invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Cannot delete paid invoices.');
        }

        $invoice->delete();

        return redirect()->route('managit.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
