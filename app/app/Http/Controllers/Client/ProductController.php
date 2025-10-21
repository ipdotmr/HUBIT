<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products grouped by category.
     */
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $groupedProducts = $products->groupBy('group')->map(function ($products, $group) {
            return [
                'name' => $group ?: 'Uncategorized',
                'slug' => \Illuminate\Support\Str::slug($group ?: 'uncategorized'),
                'description' => $this->getGroupDescription($group),
                'products' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'base_price' => $product->base_price,
                        'billing_cycles' => $product->billing_cycles,
                        'stock' => $product->stock,
                    ];
                }),
            ];
        })->values();

        return Inertia::render('Client/Products/Index', [
            'productGroups' => $groupedProducts,
        ]);
    }

    /**
     * Display the specified product with configuration options.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('Client/Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'group' => $product->group,
                'base_price' => $product->base_price,
                'billing_cycles' => $product->billing_cycles,
                'config_options' => $product->config_options,
                'stock' => $product->stock,
                'provisioner' => $product->provisioner,
            ],
        ]);
    }

    /**
     * Get description for a product group.
     */
    private function getGroupDescription($group)
    {
        $descriptions = [
            'Shared Hosting' => 'Reliable and affordable hosting solutions perfect for personal websites and small businesses.',
            'VPS Hosting' => 'Virtual Private Servers with dedicated resources for better performance and control.',
            'Dedicated Servers' => 'Powerful dedicated servers with full root access and maximum performance.',
            'Reseller Hosting' => 'Start your own hosting business with our white-label reseller packages.',
            'Domain Names' => 'Register and manage your domain names with competitive pricing.',
            'SSL Certificates' => 'Secure your website with trusted SSL certificates.',
            'Email Hosting' => 'Professional email hosting solutions for your business.',
        ];

        return $descriptions[$group] ?? 'Browse our range of products and services.';
    }
}
