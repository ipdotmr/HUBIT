<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Client;
use App\Models\Invoice;

class CartService
{
    public function getOrCreateCart(Client $client, string $currency = 'MRU'): Cart
    {
        return Cart::firstOrCreate(
            ['client_id' => $client->id],
            ['currency' => $currency, 'totals' => [], 'meta' => []]
        );
    }

    public function addDomain(Cart $cart, string $fqdn, int $years, string $registrar, float $price): CartItem
    {
        return CartItem::create([
            'cart_id' => $cart->id,
            'type' => 'domain',
            'fqdn' => $fqdn,
            'years' => $years,
            'price' => $price,
            'currency' => $cart->currency,
            'config' => [
                'registrar' => $registrar,
                'action' => 'register',
            ],
        ]);
    }

    public function addHosting(Cart $cart, int $productId, string $plan, string $cycle, float $price, array $config = []): CartItem
    {
        return CartItem::create([
            'cart_id' => $cart->id,
            'type' => 'hosting',
            'sku' => "product-{$productId}",
            'price' => $price,
            'currency' => $cart->currency,
            'config' => array_merge([
                'product_id' => $productId,
                'plan' => $plan,
                'cycle' => $cycle,
            ], $config),
        ]);
    }

    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    public function clearCart(Cart $cart): bool
    {
        $cart->items()->delete();
        return true;
    }

    public function totals(Cart $cart): array
    {
        $cart->load('items');
        $totals = $cart->calculateTotals();
        
        $cart->update(['totals' => $totals]);
        
        return $totals;
    }

    public function toInvoice(Cart $cart): Invoice
    {
        $cart->load('items', 'client');
        $totals = $this->totals($cart);

        $invoice = Invoice::create([
            'client_id' => $cart->client_id,
            'number' => $this->generateInvoiceNumber(),
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'total' => $totals['total'],
            'currency' => $cart->currency,
        ]);

        foreach ($cart->items as $item) {
            $invoice->items()->create([
                'type' => 'product',
                'description' => $this->getItemDescription($item),
                'qty' => 1,
                'unit_amount' => $item->price,
            ]);
        }

        return $invoice;
    }

    protected function generateInvoiceNumber(): string
    {
        $latest = Invoice::latest('id')->first();
        $number = $latest ? $latest->id + 1 : 1;
        
        return 'INV-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    protected function getItemDescription(CartItem $item): string
    {
        return match ($item->type) {
            'domain' => "Domain Registration: {$item->fqdn} ({$item->years} year" . ($item->years > 1 ? 's' : '') . ')',
            'hosting' => "Hosting: {$item->config['plan']} - {$item->config['cycle']}",
            'addon' => "Addon: {$item->sku}",
            default => 'Unknown item',
        };
    }
}
