<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject_en', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $templates = $query->orderBy('name')
            ->paginate(20)
            ->through(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'type' => $template->type,
                    'category' => $template->category,
                    'subject_en' => $template->subject_en,
                    'is_active' => $template->is_active,
                    'updated_at' => $template->updated_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/EmailTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'type', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/EmailTemplates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates',
            'type' => 'required|in:incoming,outgoing',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'subject_en' => 'required|string|max:255',
            'body_en' => 'required|string',
            'subject_ar' => 'nullable|string|max:255',
            'body_ar' => 'nullable|string',
            'subject_fr' => 'nullable|string|max:255',
            'body_fr' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = EmailTemplate::create($validated);

        return redirect()->route('managit.email-templates.edit', $template->id)
            ->with('success', 'Email template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return Inertia::render('Admin/EmailTemplates/Edit', [
            'template' => [
                'id' => $emailTemplate->id,
                'name' => $emailTemplate->name,
                'type' => $emailTemplate->type,
                'category' => $emailTemplate->category,
                'description' => $emailTemplate->description,
                'subject_en' => $emailTemplate->subject_en,
                'body_en' => $emailTemplate->body_en,
                'subject_ar' => $emailTemplate->subject_ar,
                'body_ar' => $emailTemplate->body_ar,
                'subject_fr' => $emailTemplate->subject_fr,
                'body_fr' => $emailTemplate->body_fr,
                'variables' => $emailTemplate->variables ?? [],
                'is_active' => $emailTemplate->is_active,
            ],
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'type' => 'required|in:incoming,outgoing',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'subject_en' => 'required|string|max:255',
            'body_en' => 'required|string',
            'subject_ar' => 'nullable|string|max:255',
            'body_ar' => 'nullable|string',
            'subject_fr' => 'nullable|string|max:255',
            'body_fr' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $emailTemplate->update($validated);

        return redirect()->route('managit.email-templates.edit', $emailTemplate->id)
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('managit.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }
}
