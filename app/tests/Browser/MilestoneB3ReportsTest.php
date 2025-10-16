<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MilestoneB3ReportsTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_services_report_displays_metrics(): void
    {
        $adminUser = User::factory()->create(['is_admin' => true]);
        $client = Client::factory()->create();

        $product1 = Product::factory()->create(['name' => 'Basic Hosting', 'group' => 'hosting']);
        $product2 = Product::factory()->create(['name' => 'VPS Server', 'group' => 'vps']);

        Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product1->id,
            'status' => 'active',
            'recurring_amount' => 29.99,
            'provisioner' => 'cpanel',
        ]);

        Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product2->id,
            'status' => 'suspended',
            'recurring_amount' => 59.99,
            'provisioner' => 'plesk',
        ]);

        $this->browse(function (Browser $browser) use ($adminUser) {
            $browser->loginAs($adminUser)
                ->visit(route('managit.reports.services'))
                ->assertSee('Services Report')
                ->assertSee('Total Services')
                ->assertSee('2')
                ->assertSee('Active Services')
                ->assertSee('1')
                ->assertSee('Suspended')
                ->assertSee('Services by Provisioner')
                ->assertSee('Services by Product')
                ->assertSee('Recent Services');
        });
    }

    public function test_admin_wallet_report_displays_metrics(): void
    {
        $adminUser = User::factory()->create(['is_admin' => true]);
        $client = Client::factory()->create();

        $wallet = Wallet::factory()->create([
            'client_id' => $client->id,
            'balance' => 100.00,
        ]);

        WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 150.00,
            'balance_after' => 150.00,
        ]);

        WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'type' => 'debit',
            'amount' => 50.00,
            'balance_after' => 100.00,
        ]);

        $this->browse(function (Browser $browser) use ($adminUser) {
            $browser->loginAs($adminUser)
                ->visit(route('managit.reports.wallet'))
                ->assertSee('Wallet Report')
                ->assertSee('Total Transactions')
                ->assertSee('2')
                ->assertSee('Total Credits')
                ->assertSee('$150.00')
                ->assertSee('Total Debits')
                ->assertSee('$50.00')
                ->assertSee('Transactions by Type')
                ->assertSee('Recent Transactions');
        });
    }

    public function test_admin_can_export_services_csv(): void
    {
        $adminUser = User::factory()->create(['is_admin' => true]);
        $client = Client::factory()->create(['company_name' => 'Acme Corp']);

        $product = Product::factory()->create(['name' => 'Basic Hosting']);

        Service::factory()->create([
            'client_id' => $client->id,
            'product_id' => $product->id,
            'status' => 'active',
            'recurring_amount' => 29.99,
        ]);

        $this->browse(function (Browser $browser) use ($adminUser) {
            $browser->loginAs($adminUser)
                ->visit(route('managit.reports.services'))
                ->assertSee('Services Report')
                ->assertSee('Export CSV');
        });
    }

    public function test_admin_can_export_wallet_csv(): void
    {
        $adminUser = User::factory()->create(['is_admin' => true]);
        $client = Client::factory()->create(['company_name' => 'Acme Corp']);

        $wallet = Wallet::factory()->create(['client_id' => $client->id]);

        WalletTransaction::factory()->create([
            'wallet_id' => $wallet->id,
            'type' => 'credit',
            'amount' => 100.00,
        ]);

        $this->browse(function (Browser $browser) use ($adminUser) {
            $browser->loginAs($adminUser)
                ->visit(route('managit.reports.wallet'))
                ->assertSee('Wallet Report')
                ->assertSee('Export CSV');
        });
    }
}
