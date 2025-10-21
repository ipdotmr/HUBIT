<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'client_id',
        'subject',
        'status',
        'priority',
        'department',
        'message',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
