<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Stage4WalletFlowTest extends DuskTestCase
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
     * E2E: Wallet top-up (mock Stripe) → balance increases → pay invoice via wallet
     *
     * @group e2e
     * @group stage4
     */
    public function test_wallet_topup_and_invoice_payment_with_mocks()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();
            $client = Client::factory()->create(['user_id' => $user->id]);

            Wallet::create([
                'client_id' => $client->id,
                'currency' => 'USD',
                'balance' => 0.00,
            ]);

            Invoice::factory()->create([
                'client_id' => $client->id,
                'status' => 'open',
                'total' => 50.00,
                'currency' => 'USD',
            ]);

            $browser->loginAs($user)
                    ->visit('/dashboard/wallet')
                    ->assertSee('Wallet')
                    ->assertSee('0.00')
                    ->type('amount', '100')
                    ->press('Add Funds')
                    ->waitFor('[data-test="payment-form"]', 10)
                    ->press('Complete Payment')
                    ->waitForLocation('/dashboard/wallet', 30)
                    ->assertSee('100.00')
                    ->visit('/dashboard/billing')
                    ->press('Pay with Wallet')
                    ->waitForText('Payment successful', 5)
                    ->visit('/dashboard/wallet')
                    ->assertSee('50.00');

            $this->assertDatabaseHas('invoices', [
                'client_id' => $client->id,
                'status' => 'paid',
            ]);
        });
    }
}
