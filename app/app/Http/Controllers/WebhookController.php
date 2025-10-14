<?php

namespace App\Http\Controllers;

use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function stripe(Request $request, StripeService $stripeService)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');
        
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        Log::info('Stripe webhook received', ['type' => $event->type, 'id' => $event->id]);
        
        switch ($event->type) {
            case 'checkout.session.completed':
                $stripeService->handleCheckoutCompleted($event->data->object->toArray());
                break;
            
            case 'payment_intent.payment_failed':
                $stripeService->handlePaymentFailed($event->data->object->toArray());
                break;
            
            case 'charge.refunded':
                $stripeService->handleChargeRefunded($event->data->object->toArray());
                break;
            
            default:
                Log::info('Stripe webhook: unhandled event type', ['type' => $event->type]);
        }
        
        return response()->json(['success' => true]);
    }
}
