<?php

namespace App\Services\Hosting;

use App\Contracts\ProvisionerInterface;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Services\Provisioners\CpanelProvisioner;
use App\Services\Provisioners\PleskProvisioner;
use Illuminate\Support\Facades\Log;

class HostingService
{
    protected array $provisioners = [];

    public function __construct()
    {
        // Initialize available provisioners
        $this->registerProvisioner('cpanel', app(CpanelProvisioner::class));
        $this->registerProvisioner('plesk', app(PleskProvisioner::class));
    }

    public function registerProvisioner(string $name, ProvisionerInterface $provisioner): void
    {
        $this->provisioners[$name] = $provisioner;
    }

    public function getProvisioner(string $name): ?ProvisionerInterface
    {
        return $this->provisioners[$name] ?? null;
    }

    public function provision(Order $order): array
    {
        $product = Product::find($order->product_id);

        if (! $product || ! $product->provisioner) {
            throw new \Exception("Product {$order->product_id} has no provisioner configured");
        }

        $provisioner = $this->getProvisioner($product->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$product->provisioner} not configured");
        }

        try {
            $service = Service::create([
                'client_id' => $order->client_id,
                'product_id' => $product->id,
                'status' => 'pending',
                'provisioner' => $product->provisioner,
                'next_due_at' => now()->add($order->cycle ?? '1 month'),
            ]);

            $result = $provisioner->provision($service);

            $service->update([
                'status' => 'active',
                'provision_ref' => $result['ref'] ?? [],
                'creds' => encrypt($result['credentials'] ?? []),
            ]);

            $order->update([
                'status' => 'completed',
                'service_id' => $service->id,
            ]);

            return [
                'success' => true,
                'service' => $service,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service provisioning failed', [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            if (isset($service)) {
                $service->update(['status' => 'failed']);
            }

            $order->update(['status' => 'failed']);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function upgrade(Service $service, Product $newProduct): array
    {
        $provisioner = $this->getProvisioner($service->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$service->provisioner} not configured");
        }

        try {
            $result = $provisioner->upgrade($service, $newProduct);

            $service->update([
                'product_id' => $newProduct->id,
                'provision_ref' => array_merge(
                    $service->provision_ref ?? [],
                    $result['ref'] ?? []
                ),
            ]);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service upgrade failed', [
                'service_id' => $service->id,
                'new_product_id' => $newProduct->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function suspend(Service $service): array
    {
        $provisioner = $this->getProvisioner($service->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$service->provisioner} not configured");
        }

        try {
            $result = $provisioner->suspend($service);

            $service->update(['status' => 'suspended']);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service suspension failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function unsuspend(Service $service): array
    {
        $provisioner = $this->getProvisioner($service->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$service->provisioner} not configured");
        }

        try {
            $result = $provisioner->unsuspend($service);

            $service->update(['status' => 'active']);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service unsuspension failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function terminate(Service $service): array
    {
        $provisioner = $this->getProvisioner($service->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$service->provisioner} not configured");
        }

        try {
            $result = $provisioner->terminate($service);

            $service->update(['status' => 'terminated']);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service termination failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function sync(Service $service): array
    {
        $provisioner = $this->getProvisioner($service->provisioner);

        if (! $provisioner) {
            throw new \Exception("Provisioner {$service->provisioner} not configured");
        }

        try {
            $result = $provisioner->sync($service);

            $service->update([
                'provision_ref' => array_merge(
                    $service->provision_ref ?? [],
                    $result['ref'] ?? []
                ),
            ]);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Service sync failed', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
