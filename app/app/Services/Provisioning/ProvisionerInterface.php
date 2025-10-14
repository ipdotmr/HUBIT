<?php

namespace App\Services\Provisioning;

use App\Models\Service;

interface ProvisionerInterface
{
    public function provision(Service $service): ProvisionResult;
    
    public function suspend(Service $service): ProvisionResult;
    
    public function unsuspend(Service $service): ProvisionResult;
    
    public function terminate(Service $service): ProvisionResult;
    
    public function resetPassword(Service $service, string $password): ProvisionResult;
    
    public function sync(Service $service): ProvisionSync;
}
