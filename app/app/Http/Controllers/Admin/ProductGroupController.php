<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductGroupController extends Controller
{
    public function index()
    {
        $groups = ProductGroup::withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/ProductGroups/Index', [
            'groups' => $groups,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ProductGroups/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
        ]);

        ProductGroup::create($validated);

        return redirect()->route('managit.product-groups.index')
            ->with('success', 'Product group created successfully');
    }

    public function edit($id)
    {
        $group = ProductGroup::withCount('products')->findOrFail($id);

        return Inertia::render('Admin/ProductGroups/Edit', [
            'group' => $group,
        ]);
    }

    public function update(Request $request, $id)
    {
        $group = ProductGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
        ]);

        $group->update($validated);

        return redirect()->route('managit.product-groups.index')
            ->with('success', 'Product group updated successfully');
    }

    public function destroy($id)
    {
        $group = ProductGroup::findOrFail($id);

        if ($group->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete product group with existing products');
        }

        $group->delete();

        return redirect()->route('managit.product-groups.index')
            ->with('success', 'Product group deleted successfully');
    }
}
