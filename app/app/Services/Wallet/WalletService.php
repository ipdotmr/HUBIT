<?php

namespace App\Services\Wallet;

use App\Models\Client;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateWallet(Client $client, string $currency = 'MRU'): Wallet
    {
        return Wallet::firstOrCreate(
            [
                'client_id' => $client->id,
                'currency' => $currency,
            ],
            ['balance' => 0]
        );
    }

    public function credit(Client $client, float $amount, string $currency, string $reference = null, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($client, $amount, $currency, $reference, $meta) {
            $wallet = $this->getOrCreateWallet($client, $currency);

            $transaction = WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'credit',
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            $wallet->increment('balance', $amount);

            return $transaction;
        });
    }

    public function debit(Client $client, float $amount, string $currency, string $reference = null, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($client, $amount, $currency, $reference, $meta) {
            $wallet = $this->getOrCreateWallet($client, $currency);

            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance');
            }

            $transaction = WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'debit',
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            $wallet->decrement('balance', $amount);

            return $transaction;
        });
    }

    public function adjust(Client $client, float $amount, string $currency, string $reference = null, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($client, $amount, $currency, $reference, $meta) {
            $wallet = $this->getOrCreateWallet($client, $currency);

            $transaction = WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'adjustment',
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            if ($amount > 0) {
                $wallet->increment('balance', $amount);
            } else {
                $wallet->decrement('balance', abs($amount));
            }

            return $transaction;
        });
    }

    public function refund(Client $client, float $amount, string $currency, string $reference = null, array $meta = []): WalletTransaction
    {
        return DB::transaction(function () use ($client, $amount, $currency, $reference, $meta) {
            $wallet = $this->getOrCreateWallet($client, $currency);

            $transaction = WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'refund',
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            $wallet->increment('balance', $amount);

            return $transaction;
        });
    }

    public function getBalance(Client $client, string $currency = 'MRU'): float
    {
        $wallet = Wallet::where('client_id', $client->id)
            ->where('currency', $currency)
            ->first();

        return $wallet ? (float) $wallet->balance : 0;
    }

    public function getTransactions(Client $client, string $currency = null, int $limit = 50): \Illuminate\Support\Collection
    {
        $query = WalletTransaction::where('client_id', $client->id);

        if ($currency) {
            $query->where('currency', $currency);
        }

        return $query->latest()
            ->limit($limit)
            ->get();
    }
}
