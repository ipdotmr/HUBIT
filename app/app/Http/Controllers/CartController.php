<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): Response
    {
        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);
        $cart->load('items');

        $totals = $this->cartService->totals($cart);

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }

    public function addDomain(Request $request)
    {
        $validated = $request->validate([
            'fqdn' => 'required|string',
            'years' => 'required|integer|min:1|max:10',
            'registrar' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);

        $item = $this->cartService->addDomain(
            $cart,
            $validated['fqdn'],
            $validated['years'],
            $validated['registrar'],
            $validated['price']
        );

        return redirect()->back()->with('success', __('Domain added to cart'));
    }

    public function addHosting(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'plan' => 'required|string',
            'cycle' => 'required|string',
            'price' => 'required|numeric|min:0',
            'config' => 'nullable|array',
        ]);

        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);

        $item = $this->cartService->addHosting(
            $cart,
            $validated['product_id'],
            $validated['plan'],
            $validated['cycle'],
            $validated['price'],
            $validated['config'] ?? []
        );

        return redirect()->back()->with('success', __('Hosting added to cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'billing_cycle' => 'required|string',
            'config_options' => 'nullable|array',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::findOrFail($validated['product_id']);
        
        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);

        $price = $product->base_price * $validated['quantity'];

        $item = $this->cartService->addHosting(
            $cart,
            $validated['product_id'],
            $product->name,
            $validated['billing_cycle'],
            $price,
            $validated['config_options'] ?? []
        );

        return redirect()->route('cart.index')->with('success', __('Product added to cart'));
    }

    public function remove(Request $request, CartItem $item)
    {
        if ($item->cart->client_id !== $request->user()->client->id) {
            abort(403);
        }

        $this->cartService->removeItem($item);

        return redirect()->back()->with('success', __('Item removed from cart'));
    }

    public function clear(Request $request)
    {
        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);

        $this->cartService->clearCart($cart);

        return redirect()->back()->with('success', __('Cart cleared'));
    }

    public function checkout(Request $request)
    {
        $client = $request->user()->client;
        $cart = $this->cartService->getOrCreateCart($client);

        if ($cart->items()->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', __('Cart is empty'));
        }

        $invoice = $this->cartService->toInvoice($cart);

        // Clear cart after creating invoice
        $this->cartService->clearCart($cart);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', __('Invoice created successfully'));
    }
}
