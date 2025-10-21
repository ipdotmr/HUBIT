<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NetworkStatusController extends Controller
{
    public function index()
    {
        $statuses = NetworkStatus::orderBy('started_at', 'desc')->get();

        return Inertia::render('Admin/NetworkStatus/Index', [
            'statuses' => $statuses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/NetworkStatus/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:operational,degraded,partial_outage,major_outage,maintenance',
            'priority' => 'required|in:low,medium,high,critical',
            'started_at' => 'required|date',
            'resolved_at' => 'nullable|date|after:started_at',
            'is_resolved' => 'boolean',
        ]);

        NetworkStatus::create($validated);

        return redirect()->route('managit.network-status.index')
            ->with('success', 'Network status created successfully');
    }

    public function edit($id)
    {
        $status = NetworkStatus::findOrFail($id);

        return Inertia::render('Admin/NetworkStatus/Edit', [
            'status' => $status,
        ]);
    }

    public function update(Request $request, $id)
    {
        $status = NetworkStatus::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:operational,degraded,partial_outage,major_outage,maintenance',
            'priority' => 'required|in:low,medium,high,critical',
            'started_at' => 'required|date',
            'resolved_at' => 'nullable|date|after:started_at',
            'is_resolved' => 'boolean',
        ]);

        $status->update($validated);

        return redirect()->route('managit.network-status.index')
            ->with('success', 'Network status updated successfully');
    }

    public function destroy($id)
    {
        $status = NetworkStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('managit.network-status.index')
            ->with('success', 'Network status deleted successfully');
    }
}
