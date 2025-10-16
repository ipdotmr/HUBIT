<?php

namespace Tests\Browser;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Stage4ProductMappingTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $settings = app(SettingsService::class);
        $settings->set('testing.use_mocks', 'true', 'boolean', false);

        Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'decimals' => 2, 'enabled' => true]);
    }

    /**
     * E2E: Admin sets product→cPanel mapping → new order uses mapped package
     *
     * @group e2e
     * @group stage4
     */
    public function test_product_mapping_used_in_provisioning()
    {
        $this->browse(function (Browser $browser) {
            $admin = User::factory()->create(['is_admin' => true]);

            $product = Product::create([
                'name' => 'VPS Pro',
                'slug' => 'vps-pro',
                'description' => 'VPS Pro hosting',
                'group' => 'hosting',
                'is_active' => true,
                'billing_cycles' => json_encode(['monthly' => 49.99]),
                'base_price' => 49.99,
                'config_options' => json_encode([]),
            ]);

            $browser->loginAs($admin)
                ->visit('/managit/products/mapping')
                ->assertSee('Product Mapping')
                ->select('product_id', $product->id)
                ->select('provisioner', 'cpanel')
                ->type('package', 'VPS_PRO_PACKAGE')
                ->press('Save Mapping')
                ->waitForText('Mapping saved', 5)
                ->press('Test Provisioning')
                ->waitForText('Test successful', 10);

            $product->refresh();
            $this->assertEquals('cpanel', $product->provisioner);

            $config = json_decode($product->provision_config, true);
            $this->assertEquals('VPS_PRO_PACKAGE', $config['package']);
        });
    }

    /**
     * Test: /managit/ has robots.txt disallow
     *
     * @group e2e
     * @group security
     */
    public function test_robots_txt_disallows_managit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/robots.txt')
                ->assertSee('Disallow: /managit/');
        });
    }

    /**
     * Test: /admin/* returns 404 (honeypot)
     *
     * @group e2e
     * @group security
     */
    public function test_admin_route_honeypot_returns_404()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin')
                ->assertSee('404');
        });
    }
}
