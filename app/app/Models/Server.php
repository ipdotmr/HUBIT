<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Server extends Model
{
    protected $fillable = [
        'name',
        'hostname',
        'ip_address',
        'type',
        'port',
        'username',
        'password',
        'access_hash',
        'use_ssl',
        'max_accounts',
        'active_accounts',
        'nameserver1',
        'nameserver2',
        'nameserver3',
        'nameserver4',
        'is_active',
        'notes',
        'last_checked_at',
        'status',
    ];

    protected $casts = [
        'use_ssl' => 'boolean',
        'is_active' => 'boolean',
        'max_accounts' => 'integer',
        'active_accounts' => 'integer',
        'port' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'access_hash',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    public function getPasswordAttribute($value)
    {
        if ($value) {
            return Crypt::decryptString($value);
        }
        return null;
    }

    public function setAccessHashAttribute($value)
    {
        if ($value) {
            $this->attributes['access_hash'] = Crypt::encryptString($value);
        }
    }

    public function getAccessHashAttribute($value)
    {
        if ($value) {
            return Crypt::decryptString($value);
        }
        return null;
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
