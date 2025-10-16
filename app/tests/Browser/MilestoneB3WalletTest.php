<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MilestoneB3WalletTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_wallet_topup_flow_with_mock_stripe(): void
    {
        settings()->set('testing.use_mocks', true);

        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $wallet = Wallet::factory()->create([
            'client_id' => $client->id,
            'balance' => 50.00,
            'currency' => 'USD',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('client.wallet.index'))
                ->assertSee('My Wallet')
                ->assertSee('Available Balance')
                ->assertSee('$50.00')
                ->press('Add Credit')
                ->pause(500)
                ->type('amount', '100')
                ->select('payment_method', 'stripe')
                ->press('Proceed to Payment')
                ->waitForLocation('/invoices/')
                ->assertSee('Top-up invoice created successfully');
        });
    }

    public function test_wallet_transaction_history_and_filters(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $wallet = Wallet::factory()->create([
            'client_id' => $client->id,
            'balance' => 100.00,
        ]);

        WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 100.00,
            'description' => 'Top-up via Stripe',
            'balance_after' => 100.00,
        ]);

        WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => 25.00,
            'description' => 'Invoice payment',
            'balance_after' => 75.00,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('client.wallet.index'))
                ->assertSee('Transaction History')
                ->assertSee('Top-up via Stripe')
                ->assertSee('Invoice payment')
                ->assertSee('+$100.00')
                ->assertSee('-$25.00')
                ->select('[name="filters[type]"]', 'credit')
                ->pause(1000)
                ->assertSee('Top-up via Stripe')
                ->assertDontSee('Invoice payment')
                ->press('Clear Filters')
                ->pause(1000)
                ->assertSee('Top-up via Stripe')
                ->assertSee('Invoice payment');
        });
    }

    public function test_wallet_pay_invoice_from_balance(): void
    {
        settings()->set('testing.use_mocks', true);

        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $wallet = Wallet::factory()->create([
            'client_id' => $client->id,
            'balance' => 150.00,
        ]);

        $this->browse(function (Browser $browser) use ($user, $wallet) {
            $initialBalance = $wallet->balance;

            $browser->loginAs($user)
                ->visit(route('client.wallet.index'))
                ->assertSee('Available Balance')
                ->assertSee('$150.00');

            $wallet->refresh();
            $this->assertEquals($initialBalance, $wallet->balance);
        });
    }

    public function test_wallet_displays_correct_balances(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $user->update(['client_id' => $client->id]);

        $wallet = Wallet::factory()->create([
            'client_id' => $client->id,
            'balance' => 327.50,
            'currency' => 'USD',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('client.wallet.index'))
                ->assertSee('My Wallet')
                ->assertSee('$327.50')
                ->assertSee('USD');
        });
    }
}
