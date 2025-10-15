<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value_encrypted',
        'type',
        'scope',
        'is_secret',
        'meta',
        'updated_by',
    ];

    protected $casts = [
        'is_secret' => 'boolean',
        'meta' => 'array',
    ];

    /**
     * Get the decrypted value
     */
    public function getValueAttribute()
    {
        if (empty($this->value_encrypted)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($this->value_encrypted);

            return match ($this->type) {
                'int' => (int) $decrypted,
                'bool' => filter_var($decrypted, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($decrypted, true),
                'array' => json_decode($decrypted, true),
                default => $decrypted,
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set and encrypt value
     */
    public function setValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['value_encrypted'] = null;

            return;
        }

        $stringValue = match ($this->type) {
            'int' => (string) $value,
            'bool' => $value ? '1' : '0',
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };

        $this->attributes['value_encrypted'] = Crypt::encryptString($stringValue);
    }

    /**
     * Get the user who last updated this setting
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get audit logs for this setting
     */
    public function auditLogs()
    {
        return SettingsAudit::where('setting_type', 'system')
            ->where('setting_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Get public settings (non-secret)
     */
    public function scopePublic($query)
    {
        return $query->where('is_secret', false);
    }

    /**
     * Scope: Get settings by scope
     */
    public function scopeByScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Get setting value with fallback
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value
     */
    public static function setValue($key, $value, $type = 'string', $isSecret = false, $userId = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'type' => $type,
                'is_secret' => $isSecret,
                'updated_by' => $userId,
            ]
        );

        $setting->value = $value;
        $setting->save();

        return $setting;
    }
}
