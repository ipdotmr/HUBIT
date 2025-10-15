<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PaymentAccount;
use App\Services\Billing\CurrencyService;
use Illuminate\Support\Facades\DB;

class OfflinePaymentService
{
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function requestBankTransfer(
        Invoice $invoice,
        int $accountId,
        float $amount,
        string $currency,
        array $evidence = []
    ): PaymentTransaction {
        $account = PaymentAccount::findOrFail($accountId);

        if ($account->type !== 'bank' || ! $account->active) {
            throw new \Exception('Invalid or inactive bank account');
        }

        $conversion = $this->currencyService->convert($amount, $currency, $invoice->currency);

        return PaymentTransaction::create([
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client_id,
            'payment_account_id' => $accountId,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'currency' => $currency,
            'amount' => $amount,
            'fx_rate' => $conversion['rate'],
            'amount_home' => $conversion['amount'],
            'evidence' => $evidence,
            'meta' => [
                'reference' => 'BT-'.strtoupper(uniqid()),
                'submitted_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function requestCash(
        Invoice $invoice,
        float $amount,
        string $currency,
        array $evidence = []
    ): PaymentTransaction {
        $conversion = $this->currencyService->convert($amount, $currency, $invoice->currency);

        return PaymentTransaction::create([
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client_id,
            'method' => 'cash',
            'status' => 'pending',
            'currency' => $currency,
            'amount' => $amount,
            'fx_rate' => $conversion['rate'],
            'amount_home' => $conversion['amount'],
            'evidence' => $evidence,
            'meta' => [
                'reference' => 'CASH-'.strtoupper(uniqid()),
                'submitted_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function review(int $transactionId, bool $approve, string $note = ''): array
    {
        return DB::transaction(function () use ($transactionId, $approve, $note) {
            $transaction = PaymentTransaction::with('invoice')->findOrFail($transactionId);

            if (in_array($transaction->status, ['approved', 'rejected'])) {
                throw new \Exception('Transaction already reviewed');
            }

            $transaction->update([
                'status' => $approve ? 'approved' : 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'meta' => array_merge($transaction->meta ?? [], [
                    'review_note' => $note,
                    'reviewed_at' => now()->toIso8601String(),
                ]),
            ]);

            if ($approve) {
                $this->processApproval($transaction);
            }

            activity()
                ->performedOn($transaction)
                ->withProperties([
                    'action' => $approve ? 'approved' : 'rejected',
                    'note' => $note,
                    'amount_home' => $transaction->amount_home,
                ])
                ->log('payment_transaction_reviewed');

            return [
                'success' => true,
                'message' => $approve ? 'Payment approved and processed' : 'Payment rejected',
                'transaction' => $transaction->fresh(),
            ];
        });
    }

    private function processApproval(PaymentTransaction $transaction): void
    {
        $invoice = $transaction->invoice;

        Payment::create([
            'invoice_id' => $invoice->id,
            'client_id' => $invoice->client_id,
            'amount' => $transaction->amount_home,
            'currency' => $invoice->currency,
            'method' => $transaction->method,
            'status' => 'completed',
            'transaction_id' => "offline-{$transaction->id}",
            'meta' => [
                'payment_transaction_id' => $transaction->id,
                'original_amount' => $transaction->amount,
                'original_currency' => $transaction->currency,
                'fx_rate' => $transaction->fx_rate,
            ],
        ]);

        $invoice->increment('paid', $transaction->amount_home);

        if ($invoice->fresh()->balance <= 0.01) {
            $invoice->update(['status' => 'paid']);

            foreach ($invoice->items as $item) {
                if ($item->provisionable) {
                    dispatch(new \App\Jobs\ProvisionService($item));
                }
            }
        }
    }
}
