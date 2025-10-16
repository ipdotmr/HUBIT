<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function log(string $action, Model $entity, array $meta = []): AuditLog
    {
        return AuditLog::create([
            'actor_type' => get_class(Auth::user()),
            'actor_id' => Auth::id(),
            'action' => $action,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->id,
            'meta' => $meta,
        ]);
    }
}
