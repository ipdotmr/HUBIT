<?php

namespace Tests\Feature\Billing;

use Tests\TestCase;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\Billing\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\CurrenciesSeeder::class);
        $this->service = app(CurrencyService::class);
    }

    public function test_can_get_enabled_currencies()
    {
        $currencies = $this->service->getEnabledCurrencies();

        $this->assertIsArray($currencies);
        $this->assertGreaterThan(0, count($currencies));
        
        foreach ($currencies as $currency) {
            $this->assertArrayHasKey('code', $currency);
            $this->assertArrayHasKey('name', $currency);
            $this->assertArrayHasKey('decimals', $currency);
        }
    }

    public function test_can_convert_currency()
    {
        $amount = $this->service->convert(100, 'MRU', 'USD');

        $this->assertIsFloat($amount);
        $this->assertGreaterThan(0, $amount);
    }

    public function test_same_currency_conversion_returns_same_amount()
    {
        $amount = $this->service->convert(100, 'MRU', 'MRU');

        $this->assertEquals(100.0, $amount);
    }

    public function test_can_get_exchange_rate()
    {
        $rate = $this->service->getRate('MRU', 'USD');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    public function test_can_refresh_rates()
    {
        $result = $this->service->refreshRates();

        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['updated']);
    }

    public function test_can_get_default_currency()
    {
        $default = $this->service->getDefaultCurrency();

        $this->assertEquals('MRU', $default);
    }
}
