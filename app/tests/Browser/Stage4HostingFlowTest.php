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

class Stage4HostingFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $settings = app(SettingsService::class);
        $settings->set('testing.use_mocks', 'true', 'boolean', false);

        Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'decimals' => 2, 'enabled' => true]);

        Product::create([
            'name' => 'Shared Hosting Basic',
            'slug' => 'shared-hosting-basic',
            'description' => 'Basic shared hosting',
            'group' => 'hosting',
            'is_active' => true,
            'billing_cycles' => json_encode(['monthly' => 9.99]),
            'base_price' => 9.99,
            'provisioner' => 'cpanel',
            'provision_config' => json_encode(['package' => 'MOCK_BASIC']),
            'config_options' => json_encode([]),
        ]);
    }

    /**
     * E2E: Hosting purchase → checkout (mock Stripe) → provisioner job → service Active
     *
     * @group e2e
     * @group stage4
     */
    public function test_hosting_provisioning_flow_with_mocks()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            $client = Client::factory()->create(['user_id' => $user->id]);

            $browser->loginAs($user)
                ->visit('/products')
                ->assertSee('Shared Hosting Basic')
                ->press('Add to Cart')
                ->waitForText('Added', 5)
                ->visit('/cart')
                ->press('Checkout')
                ->waitFor('[data-test="payment-form"]', 10)
                ->press('Complete Payment')
                ->waitForLocation('/dashboard/services', 30)
                ->assertSee('My Services')
                ->assertSee('Shared Hosting Basic')
                ->assertSee('Active');

            $this->assertDatabaseHas('services', [
                'client_id' => $client->id,
                'status' => 'active',
            ]);
        });
    }
}
