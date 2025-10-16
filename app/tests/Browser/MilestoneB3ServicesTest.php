<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MilestoneB3ServicesTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_service_upgrade_flow_with_mock_provisioner(): void
    {
        settings()->set('testing.use_mocks', true);

        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $currentProduct = Product::factory()->create([
            'name' => 'Basic Hosting',
            'group' => 'hosting',
            'is_active' => true,
        ]);

        $upgradeProduct = Product::factory()->create([
            'name' => 'Premium Hosting',
            'group' => 'hosting',
            'is_active' => true,
        ]);

        $service = Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $currentProduct->id,
            'status' => 'active',
            'provisioner' => 'cpanel',
            'recurring_amount' => 29.99,
            'billing_cycle' => 'monthly',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('client.services.index'))
                ->assertSee('My Services')
                ->assertSee('Basic Hosting')
                ->clickLink('Manage')
                ->assertSee('Service Details')
                ->assertSee('Overview')
                ->click('button:contains("Upgrade")')
                ->pause(500)
                ->assertSee('Available Upgrade Plans')
                ->assertSee('Premium Hosting')
                ->click('.cursor-pointer:contains("Premium Hosting")')
                ->pause(500)
                ->assertSee('Upgrade Confirmation')
                ->press('Proceed to Upgrade')
                ->waitForLocation('/invoices/')
                ->assertSee('Upgrade invoice created successfully');
        });
    }

    public function test_service_actions_reset_password_sync(): void
    {
        settings()->set('testing.use_mocks', true);

        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $product = Product::factory()->create(['group' => 'hosting']);
        $service = Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'provisioner' => 'cpanel',
        ]);

        $this->browse(function (Browser $browser) use ($user, $service) {
            $browser->loginAs($user)
                ->visit(route('client.services.show', $service->id))
                ->assertSee('Service Details')
                ->click('button:contains("Actions")')
                ->pause(500)
                ->assertSee('Reset Password')
                ->assertSee('Sync Service')
                ->press('Reset Password')
                ->pause(500)
                ->type('password', 'NewSecurePassword123!')
                ->press('Confirm Reset')
                ->waitForText('Password reset has been queued')
                ->press('Sync Now')
                ->waitForText('Service sync has been queued');
        });
    }

    public function test_service_suspend_unsuspend(): void
    {
        settings()->set('testing.use_mocks', true);

        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $product = Product::factory()->create(['group' => 'hosting']);
        $service = Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'provisioner' => 'cpanel',
        ]);

        $this->browse(function (Browser $browser) use ($user, $service) {
            $browser->loginAs($user)
                ->visit(route('client.services.show', $service->id))
                ->click('button:contains("Actions")')
                ->pause(500)
                ->assertSee('Suspend Service')
                ->press('Suspend Service')
                ->acceptDialog()
                ->waitForText('Service suspension has been queued');

            $service->refresh();
            $service->update(['status' => 'suspended']);

            $browser->refresh()
                ->click('button:contains("Actions")')
                ->pause(500)
                ->assertSee('Unsuspend Service')
                ->press('Unsuspend Service')
                ->waitForText('Service unsuspension has been queued');
        });
    }

    public function test_service_list_filters(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $product1 = Product::factory()->create(['name' => 'Basic Hosting', 'group' => 'hosting']);
        $product2 = Product::factory()->create(['name' => 'VPS Server', 'group' => 'vps']);

        Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product1->id,
            'status' => 'active',
        ]);

        Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product2->id,
            'status' => 'suspended',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('client.services.index'))
                ->assertSee('My Services')
                ->assertSee('Basic Hosting')
                ->assertSee('VPS Server')
                ->select('[name="filters[status]"]', 'active')
                ->pause(1000)
                ->assertSee('Basic Hosting')
                ->assertDontSee('VPS Server')
                ->press('Clear')
                ->pause(1000)
                ->assertSee('Basic Hosting')
                ->assertSee('VPS Server');
        });
    }
}
