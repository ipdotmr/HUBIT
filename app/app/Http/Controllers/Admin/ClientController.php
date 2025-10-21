<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with('user');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $clients = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'company' => $client->company,
                    'status' => $client->status,
                    'balance' => $client->balance,
                    'created_at' => $client->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company' => $validated['company'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postcode' => $validated['postcode'] ?? null,
            'country' => $validated['country'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'balance' => 0,
        ]);

        return redirect()->route('managit.clients.show', $client->id)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['user', 'services', 'domains', 'invoices', 'tickets']);

        return Inertia::render('Admin/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'company' => $client->company,
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'postcode' => $client->postcode,
                'country' => $client->country,
                'phone' => $client->phone,
                'status' => $client->status,
                'balance' => $client->balance,
                'created_at' => $client->created_at->format('Y-m-d H:i'),
                'services_count' => $client->services->count(),
                'domains_count' => $client->domains->count(),
                'invoices_count' => $client->invoices->count(),
                'tickets_count' => $client->tickets->count(),
            ],
            'services' => $client->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'product_name' => $service->product_name,
                    'status' => $service->status,
                    'billing_cycle' => $service->billing_cycle,
                    'next_due_date' => $service->next_due_date ? $service->next_due_date->format('Y-m-d') : null,
                ];
            }),
            'invoices' => $client->invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                ];
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return Inertia::render('Admin/Clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'company' => $client->company,
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'postcode' => $client->postcode,
                'country' => $client->country,
                'phone' => $client->phone,
                'status' => $client->status,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:2',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $client->update($validated);

        if ($client->user && $client->email !== $client->user->email) {
            $client->user->update(['email' => $validated['email']]);
        }

        return redirect()->route('managit.clients.show', $client->id)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        if ($client->services()->where('status', 'active')->count() > 0) {
            return back()->with('error', 'Cannot delete client with active services.');
        }

        if ($client->user) {
            $client->user->delete();
        }

        $client->delete();

        return redirect()->route('managit.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
