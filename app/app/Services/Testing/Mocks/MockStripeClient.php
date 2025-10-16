<?php

namespace App\Services\Testing\Mocks;

class MockStripeClient
{
    public function createCheckoutSession(array $params): array
    {
        usleep(100000); // 100ms realistic latency
        
        return [
            'id' => 'cs_test_mock_' . uniqid(),
            'url' => '/mock/stripe/checkout?session_id=cs_test_mock_' . uniqid(),
            'payment_status' => 'unpaid',
            'amount_total' => $params['line_items'][0]['price_data']['unit_amount'] ?? 5000,
            'currency' => $params['line_items'][0]['price_data']['currency'] ?? 'usd',
        ];
    }

    public function retrieveSession(string $sessionId): array
    {
        return [
            'id' => $sessionId,
            'payment_status' => 'paid',
            'customer' => 'cus_mock_' . uniqid(),
            'payment_intent' => 'pi_mock_' . uniqid(),
        ];
    }

    public function createPaymentIntent(array $params): array
    {
        usleep(80000);
        
        return [
            'id' => 'pi_mock_' . uniqid(),
            'amount' => $params['amount'],
            'currency' => $params['currency'],
            'status' => 'succeeded',
            'client_secret' => 'pi_mock_secret_' . uniqid(),
        ];
    }

    public function retrieveBalance(): array
    {
        return [
            'available' => [['amount' => 1000000, 'currency' => 'usd']],
            'pending' => [['amount' => 0, 'currency' => 'usd']],
        ];
    }
}
