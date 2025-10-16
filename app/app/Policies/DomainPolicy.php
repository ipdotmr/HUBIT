<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DomainPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->client !== null;
    }

    public function view(User $user, Domain $domain): bool
    {
        if (! $user->client) {
            return false;
        }

        return $domain->client_id === $user->client->id;
    }

    public function create(User $user): bool
    {
        return $user->client !== null;
    }

    public function update(User $user, Domain $domain): bool
    {
        if (! $user->client) {
            return false;
        }

        return $domain->client_id === $user->client->id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        if (! $user->client) {
            return false;
        }

        return $domain->client_id === $user->client->id;
    }

    public function restore(User $user, Domain $domain): bool
    {
        if (! $user->client) {
            return false;
        }

        return $domain->client_id === $user->client->id;
    }

    public function forceDelete(User $user, Domain $domain): bool
    {
        return false;
    }
}
