<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'MRU', 'name' => 'Mauritanian Ouguiya', 'decimals' => 2, 'enabled' => true],
            ['code' => 'USD', 'name' => 'US Dollar', 'decimals' => 2, 'enabled' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'decimals' => 2, 'enabled' => true],
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
