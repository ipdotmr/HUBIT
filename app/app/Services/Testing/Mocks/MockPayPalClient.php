<?php

namespace App\Services\Testing\Mocks;

class MockPayPalClient
{
    public function createOrder(array $params): array
    {
        usleep(120000); // 120ms latency
        
        return [
            'id' => 'PAYPAL-MOCK-' . strtoupper(uniqid()),
            'status' => 'CREATED',
            'links' => [
                ['rel' => 'approve', 'href' => '/mock/paypal/approve?token=' . uniqid()],
                ['rel' => 'capture', 'href' => '/mock/paypal/capture'],
            ],
        ];
    }

    public function captureOrder(string $orderId): array
    {
        usleep(150000);
        
        return [
            'id' => $orderId,
            'status' => 'COMPLETED',
            'purchase_units' => [
                [
                    'payments' => [
                        'captures' => [
                            [
                                'id' => 'CAPTURE-MOCK-' . uniqid(),
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '50.00', 'currency_code' => 'USD'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
