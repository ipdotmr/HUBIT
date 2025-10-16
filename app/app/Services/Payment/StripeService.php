<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Customer;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Invoice $invoice): ?string
    {
        try {
            $client = $invoice->client;

            $customer = $this->getOrCreateCustomer($client);

            $lineItems = $invoice->invoice_items->map(function ($item) {
                return [
                    'price_data' => [
                        'currency' => strtolower($invoice->currency),
                        'product_data' => [
                            'name' => $item->description,
                        ],
                        'unit_amount' => (int) ($item->unit_amount * 100),
                    ],
                    'quantity' => $item->quantity,
                ];
            })->toArray();

            $session = CheckoutSession::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('client.invoices.success', ['invoice' => $invoice->id]).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('client.invoices.show', ['invoice' => $invoice->id]),
                'client_reference_id' => $invoice->id,
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'client_id' => $client->id,
                ],
            ]);

            return $session->url;
        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function handleCheckoutCompleted(array $session): void
    {
        try {
            $invoiceId = $session['client_reference_id'] ?? $session['metadata']['invoice_id'] ?? null;

            if (! $invoiceId) {
                Log::warning('Stripe webhook: invoice_id not found in session', ['session' => $session]);

                return;
            }

            $invoice = Invoice::find($invoiceId);

            if (! $invoice) {
                Log::warning('Stripe webhook: invoice not found', ['invoice_id' => $invoiceId]);

                return;
            }

            if ($invoice->status === 'paid') {
                Log::info('Stripe webhook: invoice already paid', ['invoice_id' => $invoiceId]);

                return;
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'provider' => 'stripe',
                'provider_ref' => $session['id'],
                'amount' => $invoice->total,
                'currency' => $invoice->currency,
                'status' => 'completed',
                'paid_at' => now(),
                'meta' => $session,
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid' => $invoice->total,
            ]);

            $this->provisionServices($invoice);

            Log::info('Stripe webhook: payment processed successfully', [
                'invoice_id' => $invoiceId,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: checkout.session.completed error', [
                'error' => $e->getMessage(),
                'session' => $session,
            ]);
        }
    }

    public function handlePaymentFailed(array $paymentIntent): void
    {
        try {
            Log::warning('Stripe payment failed', ['payment_intent' => $paymentIntent]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: payment_intent.payment_failed error', ['error' => $e->getMessage()]);
        }
    }

    public function handleChargeRefunded(array $charge): void
    {
        try {
            $payment = Payment::where('provider_ref', $charge['id'])->first();

            if ($payment) {
                $payment->update([
                    'status' => 'refunded',
                    'meta' => array_merge($payment->meta ?? [], ['refund' => $charge]),
                ]);

                Log::info('Stripe charge refunded', ['payment_id' => $payment->id]);
            }
        } catch (\Exception $e) {
            Log::error('Stripe webhook: charge.refunded error', ['error' => $e->getMessage()]);
        }
    }

    private function getOrCreateCustomer($client): Customer
    {
        $stripeCustomerId = $client->meta['stripe_customer_id'] ?? null;

        if ($stripeCustomerId) {
            try {
                return Customer::retrieve($stripeCustomerId);
            } catch (\Exception $e) {
                Log::warning('Stripe customer not found, creating new', ['customer_id' => $stripeCustomerId]);
            }
        }

        $customer = Customer::create([
            'email' => $client->email,
            'name' => $client->first_name.' '.$client->last_name,
            'metadata' => [
                'client_id' => $client->id,
            ],
        ]);

        $client->update([
            'meta' => array_merge($client->meta ?? [], ['stripe_customer_id' => $customer->id]),
        ]);

        return $customer;
    }

    private function provisionServices(Invoice $invoice): void
    {
        foreach ($invoice->invoice_items as $item) {
            if ($item->type === 'product') {
                $service = \App\Models\Service::where('invoice_id', $invoice->id)
                    ->where('status', 'pending')
                    ->first();

                if ($service && $service->provisioner) {
                    $provisioner = app("App\\Services\\Provisioning\\{$service->provisioner}Provisioner");
                    $result = $provisioner->provision($service);

                    if (! $result->success) {
                        Log::error('Auto-provisioning failed', [
                            'service_id' => $service->id,
                            'error' => $result->error,
                        ]);
                    }
                }
            }
        }
    }
}
