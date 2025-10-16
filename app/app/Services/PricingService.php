<?php

namespace App\Services;

use App\Models\Domain;

class PricingService
{
    public function getDomainRenewalPricing(Domain $domain, int $years = 1): array
    {
        $pricePerYear = 12.00;
        $subtotal = $pricePerYear * $years;
        $taxRate = 0.15;
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;

        return [
            'currency' => 'USD',
            'price_per_year' => $pricePerYear,
            'years' => $years,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }
}
