<?php

namespace App\Services\Billing;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CurrencyService
{
    public function convert(float $amount, string $from, string $to): array
    {
        if ($from === $to) {
            return [
                'amount' => $amount,
                'rate' => 1.0,
                'timestamp' => now(),
            ];
        }

        $rate = $this->getRate($from, $to);

        return [
            'amount' => round($amount * $rate, 2),
            'rate' => $rate,
            'timestamp' => now(),
        ];
    }

    public function getRate(string $from, string $to): float
    {
        $cacheKey = "fx_rate_{$from}_{$to}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to) {
            $exchangeRate = ExchangeRate::where('base', $from)
                ->where('quote', $to)
                ->latest('fetched_at')
                ->first();

            if ($exchangeRate) {
                return (float) $exchangeRate->rate;
            }

            $inverseRate = ExchangeRate::where('base', $to)
                ->where('quote', $from)
                ->latest('fetched_at')
                ->first();

            if ($inverseRate) {
                return 1.0 / (float) $inverseRate->rate;
            }

            return 1.0;
        });
    }

    public function refreshRates(): array
    {
        $results = [];
        $useManual = settings()->get('currency.use_manual_rates', true);

        if ($useManual) {
            $results = $this->refreshManualRates();
        } else {
            $results = $this->refreshProviderRates();
        }

        Cache::flush();

        return $results;
    }

    private function refreshManualRates(): array
    {
        $pairs = [
            ['MRU', 'USD', settings()->get('currency.rate_mru_usd', 36.50)],
            ['MRU', 'EUR', settings()->get('currency.rate_mru_eur', 40.00)],
            ['USD', 'EUR', settings()->get('currency.rate_usd_eur', 0.92)],
        ];

        $results = [];

        foreach ($pairs as [$base, $quote, $rate]) {
            ExchangeRate::updateOrCreate(
                ['base' => $base, 'quote' => $quote],
                [
                    'rate' => $rate,
                    'fetched_at' => now(),
                    'meta' => ['source' => 'manual', 'updated_by' => auth()->id()],
                ]
            );

            $results[] = "{$base}/{$quote}: {$rate}";
        }

        return ['success' => true, 'rates' => $results, 'source' => 'manual'];
    }

    private function refreshProviderRates(): array
    {
        return ['success' => false, 'message' => 'Provider integration not implemented yet'];
    }

    public function getEnabledCurrencies(): array
    {
        return Cache::remember('enabled_currencies', 3600, function () {
            return Currency::where('enabled', true)->get()->toArray();
        });
    }

    public function getDefaultCurrency(): string
    {
        return settings()->get('currency.default', 'MRU');
    }
}
