<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('group');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('group') && $request->group) {
            $query->where('product_group_id', $request->group);
        }

        $products = $query->orderBy('name')
            ->paginate(20)
            ->through(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'group_name' => $product->group ? $product->group->name : 'N/A',
                    'price' => number_format($product->price, 2),
                    'billing_cycle' => $product->billing_cycle,
                    'status' => $product->status,
                ];
            });

        $groups = ProductGroup::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'groups' => $groups,
            'filters' => $request->only(['search', 'group']),
        ]);
    }

    public function create()
    {
        $groups = ProductGroup::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Create', [
            'groups' => $groups,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_group_id' => 'required|exists:product_groups,id',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,semi-annually,annually,biennially,triennially',
            'setup_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'product_group_id' => $validated['product_group_id'],
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'setup_fee' => $validated['setup_fee'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()->route('managit.products.show', $product->id)
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('group');

        return Inertia::render('Admin/Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'group_name' => $product->group ? $product->group->name : 'N/A',
                'price' => number_format($product->price, 2),
                'billing_cycle' => $product->billing_cycle,
                'setup_fee' => number_format($product->setup_fee, 2),
                'status' => $product->status,
            ],
        ]);
    }

    public function edit(Product $product)
    {
        $groups = ProductGroup::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'product_group_id' => $product->product_group_id,
                'price' => $product->price,
                'billing_cycle' => $product->billing_cycle,
                'setup_fee' => $product->setup_fee,
                'status' => $product->status,
            ],
            'groups' => $groups,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_group_id' => 'required|exists:product_groups,id',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,semi-annually,annually,biennially,triennially',
            'setup_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'product_group_id' => $validated['product_group_id'],
            'price' => $validated['price'],
            'billing_cycle' => $validated['billing_cycle'],
            'setup_fee' => $validated['setup_fee'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()->route('managit.products.show', $product->id)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('managit.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
