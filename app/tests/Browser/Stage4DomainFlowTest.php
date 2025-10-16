<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Stage4DomainFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $settings = app(SettingsService::class);
        $settings->set('testing.use_mocks', 'true', 'boolean', false);

        Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'decimals' => 2, 'enabled' => true]);
        Currency::create(['code' => 'MRU', 'name' => 'Mauritanian Ouguiya', 'decimals' => 2, 'enabled' => true]);
        Currency::create(['code' => 'EUR', 'name' => 'Euro', 'decimals' => 2, 'enabled' => true]);

        Product::create([
            'name' => 'Domain Registration',
            'slug' => 'domain-registration',
            'description' => 'Domain registration service',
            'group' => 'domain',
            'is_active' => true,
            'billing_cycles' => json_encode(['annual' => 15.00]),
            'base_price' => 15.00,
            'config_options' => json_encode([]),
        ]);
    }

    /**
     * E2E Test: Domain search → cart → checkout (mock Stripe) → domain Active
     *
     * @group e2e
     * @group stage4
     */
    public function test_domain_registration_flow_with_mocks()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create(['email' => 'test@example.com']);
            $client = Client::factory()->create(['user_id' => $user->id]);

            $testDomain = 'test-domain-'.time().'.com';

            $browser->loginAs($user)
                ->visit('/domains/search')
                ->assertSee('Domain Search')
                ->type('domain', $testDomain)
                ->press('Search')
                ->waitForText('Available', 10)
                ->assertSee('$15.00')
                ->press('Add to Cart')
                ->waitForText('Added', 5)
                ->visit('/cart')
                ->assertSee('Shopping Cart')
                ->assertSee($testDomain)
                ->assertSee('15.00')
                ->press('Checkout')
                ->waitFor('[data-test="payment-form"]', 10)
                ->press('Complete Payment')
                ->waitForLocation('/dashboard/domains', 30)
                ->assertSee('My Domains')
                ->assertSee($testDomain)
                ->assertSee('Active');

            $this->assertDatabaseHas('domain_orders', [
                'client_id' => $client->id,
                'status' => 'active',
            ]);

            $this->assertDatabaseHas('invoices', [
                'client_id' => $client->id,
                'status' => 'paid',
            ]);
        });
    }

    /**
     * Test: Domain search renders in English
     *
     * @group e2e
     * @group i18n
     */
    public function test_domain_search_renders_in_english()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            Client::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                ->visit('/domains/search')
                ->assertAttribute('html', 'lang', 'en')
                ->assertSee('Domain Search')
                ->assertMissing('[data-error="console"]');
        });
    }

    /**
     * Test: Domain search renders in Arabic (RTL)
     *
     * @group e2e
     * @group i18n
     */
    public function test_domain_search_renders_in_arabic_rtl()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            Client::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                ->visit('/language/ar')
                ->visit('/domains/search')
                ->assertAttribute('html', 'dir', 'rtl')
                ->assertAttribute('html', 'lang', 'ar')
                ->assertMissing('[data-error="console"]');
        });
    }

    /**
     * Test: Domain search renders in French
     *
     * @group e2e
     * @group i18n
     */
    public function test_domain_search_renders_in_french()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            Client::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                ->visit('/language/fr')
                ->visit('/domains/search')
                ->assertAttribute('html', 'lang', 'fr')
                ->assertMissing('[data-error="console"]');
        });
    }
}
