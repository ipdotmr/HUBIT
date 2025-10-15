<?php

namespace App\Services\Billing;

use App\Models\PaymentAccount;
use Illuminate\Support\Facades\Gate;

class PaymentAccountService
{
    public function getAll(array $filters = [])
    {
        Gate::authorize('viewAny', PaymentAccount::class);

        $query = PaymentAccount::query()->with(['currency', 'creator']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getForClient(string $currency = null, string $lang = 'en'): array
    {
        $query = PaymentAccount::where('active', true);

        if ($currency) {
            $query->where('currency', $currency);
        }

        return $query->get()->map(function ($account) use ($lang) {
            return $account->getDisplayInstructions($lang);
        })->toArray();
    }

    public function create(array $data)
    {
        Gate::authorize('create', PaymentAccount::class);

        $data['created_by'] = auth()->id();

        $account = PaymentAccount::create($data);

        activity()
            ->performedOn($account)
            ->withProperties(['attributes' => $data])
            ->log('payment_account_created');

        return $account;
    }

    public function update(PaymentAccount $account, array $data)
    {
        Gate::authorize('update', $account);

        $original = $account->toArray();
        $account->update($data);

        activity()
            ->performedOn($account)
            ->withProperties(['old' => $original, 'new' => $account->fresh()->toArray()])
            ->log('payment_account_updated');

        return $account;
    }

    public function delete(PaymentAccount $account)
    {
        Gate::authorize('delete', $account);

        activity()
            ->performedOn($account)
            ->withProperties(['attributes' => $account->toArray()])
            ->log('payment_account_deleted');

        return $account->delete();
    }
}
