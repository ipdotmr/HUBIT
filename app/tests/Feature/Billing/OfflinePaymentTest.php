<?php

namespace Tests\Feature\Billing;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\PaymentAccount;
use App\Models\User;
use App\Services\Billing\OfflinePaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group quarantine
 */
class OfflinePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Client $client;

    protected Invoice $invoice;

    protected PaymentAccount $account;

    protected OfflinePaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\CurrenciesSeeder::class);

        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();

        $this->invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'total' => 1000,
            'currency' => 'MRU',
            'status' => 'open',
        ]);

        $this->account = PaymentAccount::factory()->create([
            'currency' => 'MRU',
            'type' => 'bank_transfer',
            'enabled' => true,
        ]);

        $this->service = app(OfflinePaymentService::class);
    }

    public function test_can_request_bank_transfer_payment()
    {
        $transaction = $this->service->requestBankTransfer(
            $this->invoice,
            $this->account->id,
            1000,
            'MRU',
            []
        );

        $this->assertNotNull($transaction);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals('bank_transfer', $transaction->method);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals('MRU', $transaction->currency);
        $this->assertStringStartsWith('BT-', $transaction->meta['reference']);
    }

    public function test_can_request_cash_payment()
    {
        $transaction = $this->service->requestCash(
            $this->invoice,
            1000,
            'MRU',
            []
        );

        $this->assertNotNull($transaction);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals('cash', $transaction->method);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertStringStartsWith('CASH-', $transaction->meta['reference']);
    }

    public function test_can_approve_transaction()
    {
        $transaction = $this->service->requestBankTransfer(
            $this->invoice,
            $this->account->id,
            1000,
            'MRU',
            []
        );

        $result = $this->service->review($transaction->id, true, 'Approved');

        $this->assertTrue($result['success']);
        $this->assertEquals('approved', $transaction->fresh()->status);
    }

    public function test_can_reject_transaction()
    {
        $transaction = $this->service->requestBankTransfer(
            $this->invoice,
            $this->account->id,
            1000,
            'MRU',
            []
        );

        $result = $this->service->review($transaction->id, false, 'Invalid proof');

        $this->assertTrue($result['success']);
        $this->assertEquals('rejected', $transaction->fresh()->status);
    }

    public function test_converts_foreign_currency_to_home_currency()
    {
        $usdAccount = PaymentAccount::factory()->create([
            'currency' => 'USD',
            'type' => 'bank_transfer',
            'enabled' => true,
        ]);

        $transaction = $this->service->requestBankTransfer(
            $this->invoice,
            $usdAccount->id,
            100,
            'USD',
            []
        );

        $this->assertEquals('USD', $transaction->currency);
        $this->assertEquals(100, $transaction->amount);
        $this->assertNotNull($transaction->fx_rate);
        $this->assertNotNull($transaction->amount_home);
    }
}
