<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;

class WalletPolicy
{
    public function view(User $user, Wallet $wallet): bool
    {
        return $user->client && $user->client->id === $wallet->client_id;
    }

    public function update(User $user, Wallet $wallet): bool
    {
        return $user->client && $user->client->id === $wallet->client_id;
    }
}
