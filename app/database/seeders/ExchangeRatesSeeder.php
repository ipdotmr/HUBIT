<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class ExchangeRatesSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['base' => 'MRU', 'quote' => 'USD', 'rate' => 0.0274],
            ['base' => 'MRU', 'quote' => 'EUR', 'rate' => 0.0250],
            ['base' => 'USD', 'quote' => 'EUR', 'rate' => 0.92],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::updateOrCreate(
                ['base' => $rate['base'], 'quote' => $rate['quote']],
                array_merge($rate, [
                    'fetched_at' => Carbon::now(),
                    'meta' => ['source' => 'manual', 'seeded' => true]
                ])
            );
        }
    }
}
