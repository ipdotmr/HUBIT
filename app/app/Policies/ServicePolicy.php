<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->client !== null;
    }

    public function view(User $user, Service $service): bool
    {
        return $user->client && $user->client->id === $service->client_id;
    }

    public function update(User $user, Service $service): bool
    {
        return $user->client && $user->client->id === $service->client_id;
    }

    public function suspend(User $user, Service $service): bool
    {
        return $user->client && $user->client->id === $service->client_id;
    }
}
