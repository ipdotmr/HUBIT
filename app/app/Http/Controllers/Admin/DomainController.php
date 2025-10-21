<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $query = Domain::with('client');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
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

        $domains = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($domain) {
                return [
                    'id' => $domain->id,
                    'domain' => $domain->domain,
                    'client_name' => $domain->client ? $domain->client->name : 'N/A',
                    'registration_date' => $domain->registered_at ? (is_string($domain->registered_at) ? $domain->registered_at : $domain->registered_at->format('Y-m-d')) : 'N/A',
                    'expiry_date' => $domain->expires_at ? (is_string($domain->expires_at) ? $domain->expires_at : $domain->expires_at->format('Y-m-d')) : 'N/A',
                    'status' => $domain->status ?? 'active',
                ];
            });

        return Inertia::render('Admin/Domains/Index', [
            'domains' => $domains,
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

        return Inertia::render('Admin/Domains/Create', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'domain' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'registrar' => 'nullable|string|max:255',
            'nameservers' => 'nullable|array',
        ]);

        $domain = Domain::create([
            'client_id' => $validated['client_id'],
            'domain' => $validated['domain'],
            'registered_at' => $validated['registration_date'],
            'expires_at' => $validated['expiry_date'],
            'registrar' => $validated['registrar'] ?? null,
            'nameservers' => $validated['nameservers'] ?? [],
            'status' => 'active',
        ]);

        return redirect()->route('managit.domains.show', $domain->id)
            ->with('success', 'Domain created successfully.');
    }

    public function show(Domain $domain)
    {
        $domain->load('client');

        return Inertia::render('Admin/Domains/Show', [
            'domain' => [
                'id' => $domain->id,
                'domain' => $domain->domain,
                'client_name' => $domain->client ? $domain->client->name : 'N/A',
                'client_email' => $domain->client ? $domain->client->email : 'N/A',
                'registration_date' => $domain->registered_at ? (is_string($domain->registered_at) ? $domain->registered_at : $domain->registered_at->format('Y-m-d')) : 'N/A',
                'expiry_date' => $domain->expires_at ? (is_string($domain->expires_at) ? $domain->expires_at : $domain->expires_at->format('Y-m-d')) : 'N/A',
                'registrar' => $domain->registrar ?? 'N/A',
                'nameservers' => $domain->nameservers ?? [],
                'status' => $domain->status ?? 'active',
            ],
        ]);
    }

    public function edit(Domain $domain)
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

        return Inertia::render('Admin/Domains/Edit', [
            'domain' => [
                'id' => $domain->id,
                'client_id' => $domain->client_id,
                'domain' => $domain->domain,
                'registration_date' => $domain->registered_at ? (is_string($domain->registered_at) ? $domain->registered_at : $domain->registered_at->format('Y-m-d')) : '',
                'expiry_date' => $domain->expires_at ? (is_string($domain->expires_at) ? $domain->expires_at : $domain->expires_at->format('Y-m-d')) : '',
                'registrar' => $domain->registrar ?? '',
                'nameservers' => $domain->nameservers ?? [],
                'status' => $domain->status ?? 'active',
            ],
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'domain' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'registrar' => 'nullable|string|max:255',
            'nameservers' => 'nullable|array',
            'status' => 'required|in:active,expired,cancelled,pending',
        ]);

        $domain->update([
            'client_id' => $validated['client_id'],
            'domain' => $validated['domain'],
            'registered_at' => $validated['registration_date'],
            'expires_at' => $validated['expiry_date'],
            'registrar' => $validated['registrar'] ?? null,
            'nameservers' => $validated['nameservers'] ?? [],
            'status' => $validated['status'],
        ]);

        return redirect()->route('managit.domains.show', $domain->id)
            ->with('success', 'Domain updated successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('managit.domains.index')
            ->with('success', 'Domain deleted successfully.');
    }
}
