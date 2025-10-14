<?php

namespace App\Services\Provisioning;

class ProvisionSync
{
    public function __construct(
        public bool $success,
        public ?array $usage = null,
        public ?string $status = null,
        public ?string $error = null
    ) {}
    
    public static function success(array $usage, string $status): self
    {
        return new self(true, $usage, $status);
    }
    
    public static function failure(string $error): self
    {
        return new self(false, null, null, $error);
    }
}
