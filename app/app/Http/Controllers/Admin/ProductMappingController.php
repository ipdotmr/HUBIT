<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductMappingController extends Controller
{
    public function index(): Response
    {
        $products = Product::with('productGroup')
            ->where('type', 'hosting')
            ->get();

        $provisioners = [
            'cpanel' => 'cPanel/WHM',
            'plesk' => 'Plesk',
        ];

        return Inertia::render('Admin/ProductMapping/Index', [
            'products' => $products,
            'provisioners' => $provisioners,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'provisioner' => 'required|string|in:cpanel,plesk',
            'package_name' => 'required|string',
            'server_id' => 'nullable|integer',
            'meta' => 'nullable|array',
        ]);

        $product->update([
            'provisioner' => $validated['provisioner'],
            'provision_config' => [
                'package' => $validated['package_name'],
                'server_id' => $validated['server_id'] ?? null,
                'meta' => $validated['meta'] ?? [],
            ],
        ]);

        return redirect()->back()->with('success', __('Product mapping updated'));
    }

    public function testProvision(Request $request, Product $product)
    {
        $validated = $request->validate([
            'domain' => 'required|string',
        ]);

        // TODO: Implement test provisioning
        // This would create a test service and attempt to provision it
        // Then rollback or terminate the test service

        return response()->json([
            'success' => true,
            'message' => 'Test provisioning would run here',
        ]);
    }
}
