<?php

namespace App\Services\Provisioning;

class ProvisionResult
{
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public ?array $data = null,
        public ?string $error = null
    ) {}
    
    public static function success(?string $message = null, ?array $data = null): self
    {
        return new self(true, $message, $data);
    }
    
    public static function failure(string $error): self
    {
        return new self(false, null, null, $error);
    }
}
