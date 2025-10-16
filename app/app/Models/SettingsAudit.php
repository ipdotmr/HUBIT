<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsAudit extends Model
{
    use HasFactory;

    protected $table = 'settings_audit';

    protected $fillable = [
        'setting_type',
        'setting_id',
        'key',
        'old_value',
        'new_value',
        'changed_by',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the user who made this change
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
