<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProvisioningLogService
{
    public function start(string $operation, Model $entity, array $meta = []): void
    {
        Log::info("Provisioning started: {$operation}", array_merge([
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
        ], $meta));
    }

    public function success(string $operation, Model $entity, array $meta = []): void
    {
        Log::info("Provisioning succeeded: {$operation}", array_merge([
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
        ], $meta));
    }

    public function failed(string $operation, Model $entity, array $meta = []): void
    {
        Log::error("Provisioning failed: {$operation}", array_merge([
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
        ], $meta));
    }
}
