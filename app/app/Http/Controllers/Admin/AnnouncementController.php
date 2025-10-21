<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Announcements/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if ($validated['is_published'] ?? false) {
            $validated['published_at'] = now();
        }

        Announcement::create($validated);

        return redirect()->route('managit.announcements.index')
            ->with('success', 'Announcement created successfully');
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);

        return Inertia::render('Admin/Announcements/Edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        if (($validated['is_published'] ?? false) && !$announcement->published_at) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return redirect()->route('managit.announcements.index')
            ->with('success', 'Announcement updated successfully');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('managit.announcements.index')
            ->with('success', 'Announcement deleted successfully');
    }
}
