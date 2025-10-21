<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::orderBy('name')->get();

        return Inertia::render('Admin/Servers/Index', [
            'servers' => $servers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Servers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hostname' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:cpanel,plesk,directadmin,custom',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'access_hash' => 'nullable|string',
            'use_ssl' => 'boolean',
            'max_accounts' => 'required|integer|min:0',
            'nameserver1' => 'nullable|string|max:255',
            'nameserver2' => 'nullable|string|max:255',
            'nameserver3' => 'nullable|string|max:255',
            'nameserver4' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        Server::create($validated);

        return redirect()->route('managit.servers.index')
            ->with('success', 'Server created successfully');
    }

    public function edit($id)
    {
        $server = Server::findOrFail($id);

        return Inertia::render('Admin/Servers/Edit', [
            'server' => $server,
        ]);
    }

    public function update(Request $request, $id)
    {
        $server = Server::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hostname' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'type' => 'required|in:cpanel,plesk,directadmin,custom',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'access_hash' => 'nullable|string',
            'use_ssl' => 'boolean',
            'max_accounts' => 'required|integer|min:0',
            'nameserver1' => 'nullable|string|max:255',
            'nameserver2' => 'nullable|string|max:255',
            'nameserver3' => 'nullable|string|max:255',
            'nameserver4' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        if (empty($validated['access_hash'])) {
            unset($validated['access_hash']);
        }

        $server->update($validated);

        return redirect()->route('managit.servers.index')
            ->with('success', 'Server updated successfully');
    }

    public function destroy($id)
    {
        $server = Server::findOrFail($id);

        if ($server->services()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete server with active services');
        }

        $server->delete();

        return redirect()->route('managit.servers.index')
            ->with('success', 'Server deleted successfully');
    }

    public function testConnection($id)
    {
        $server = Server::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Connection test feature coming soon',
        ]);
    }
}
