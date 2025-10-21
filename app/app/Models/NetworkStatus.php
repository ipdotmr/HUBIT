<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkStatus extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'started_at',
        'resolved_at',
        'is_resolved',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_resolved' => 'boolean',
    ];

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'operational' => 'bg-success',
            'degraded' => 'bg-warning',
            'partial_outage' => 'bg-warning',
            'major_outage' => 'bg-danger',
            'maintenance' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'low' => 'bg-info',
            'medium' => 'bg-warning',
            'high' => 'bg-danger',
            'critical' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
