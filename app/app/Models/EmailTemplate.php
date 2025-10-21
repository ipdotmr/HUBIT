<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'category',
        'description',
        'subject_en',
        'body_en',
        'subject_ar',
        'body_ar',
        'subject_fr',
        'body_fr',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function getSubject($locale = 'en')
    {
        $field = "subject_{$locale}";
        return $this->$field ?? $this->subject_en;
    }

    public function getBody($locale = 'en')
    {
        $field = "body_{$locale}";
        return $this->$field ?? $this->body_en;
    }

    public function render($locale = 'en', array $data = [])
    {
        $subject = $this->getSubject($locale);
        $body = $this->getBody($locale);

        foreach ($data as $key => $value) {
            $subject = str_replace("{{" . $key . "}}", $value, $subject);
            $body = str_replace("{{" . $key . "}}", $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
