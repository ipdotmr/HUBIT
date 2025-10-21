<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportStatusController extends Controller
{
    public function index()
    {
        $statuses = SupportStatus::orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        return Inertia::render('Admin/SupportStatuses/Index', [
            'statuses' => $statuses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SupportStatuses/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
            'is_closed' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        SupportStatus::create($validated);

        return redirect()->route('managit.support-statuses.index')
            ->with('success', 'Support status created successfully');
    }

    public function edit($id)
    {
        $status = SupportStatus::findOrFail($id);

        return Inertia::render('Admin/SupportStatuses/Edit', [
            'status' => $status,
        ]);
    }

    public function update(Request $request, $id)
    {
        $status = SupportStatus::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
            'is_closed' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $status->update($validated);

        return redirect()->route('managit.support-statuses.index')
            ->with('success', 'Support status updated successfully');
    }

    public function destroy($id)
    {
        $status = SupportStatus::findOrFail($id);

        if ($status->tickets()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete status with existing tickets');
        }

        $status->delete();

        return redirect()->route('managit.support-statuses.index')
            ->with('success', 'Support status deleted successfully');
    }
}
