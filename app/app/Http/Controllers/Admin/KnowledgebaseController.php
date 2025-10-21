<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KnowledgebaseController extends Controller
{
    public function index()
    {
        $categories = KnowledgebaseCategory::withCount('articles')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Knowledgebase/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = KnowledgebaseCategory::orderBy('name')->get();

        return Inertia::render('Admin/Knowledgebase/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:knowledgebase_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        KnowledgebaseArticle::create($validated);

        return redirect()->route('managit.knowledgebase.index')
            ->with('success', 'Article created successfully');
    }

    public function edit($id)
    {
        $article = KnowledgebaseArticle::with('category')->findOrFail($id);
        $categories = KnowledgebaseCategory::orderBy('name')->get();

        return Inertia::render('Admin/Knowledgebase/Edit', [
            'article' => $article,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = KnowledgebaseArticle::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:knowledgebase_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean',
            'display_order' => 'required|integer|min:0',
        ]);

        $article->update($validated);

        return redirect()->route('managit.knowledgebase.index')
            ->with('success', 'Article updated successfully');
    }

    public function destroy($id)
    {
        $article = KnowledgebaseArticle::findOrFail($id);
        $article->delete();

        return redirect()->route('managit.knowledgebase.index')
            ->with('success', 'Article deleted successfully');
    }
}
