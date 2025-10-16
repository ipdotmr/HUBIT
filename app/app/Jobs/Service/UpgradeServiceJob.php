<?php

namespace App\Jobs\Service;

use App\Models\Product;
use App\Models\Service;
use App\Services\ProvisioningLogService;
use App\Services\Testing\MockFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpgradeServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 120, 300];

    public function __construct(
        public Service $service,
        public Product $newProduct
    ) {}

    public function handle(ProvisioningLogService $logger): void
    {
        $idempotencyKey = $this->getIdempotencyKey();

        $logger->start('service.upgrade', $this->service, [
            'old_product_id' => $this->service->product_id,
            'new_product_id' => $this->newProduct->id,
            'idempotency_key' => $idempotencyKey,
        ]);

        try {
            $provisioner = $this->getProvisioner();

            $result = $provisioner->upgrade($this->service, $this->newProduct);

            if ($result->success) {
                $this->service->update([
                    'product_id' => $this->newProduct->id,
                    'config' => array_merge($this->service->config ?? [], $result->meta ?? []),
                ]);

                $logger->success('service.upgrade', $this->service, [
                    'new_product_id' => $this->newProduct->id,
                    'result' => $result->toArray(),
                ]);
            } else {
                throw new \Exception($result->error ?? 'Upgrade failed');
            }
        } catch (\Exception $e) {
            $logger->failure('service.upgrade', $this->service, $e);
            throw $e;
        }
    }

    protected function getProvisioner()
    {
        if (settings()->get('testing.use_mocks')) {
            return MockFactory::provisioner($this->service->provisioner);
        }

        return app("App\\Services\\Provisioning\\{$this->service->provisioner}Provisioner");
    }

    protected function getIdempotencyKey(): string
    {
        return "upgrade_{$this->service->id}_{$this->newProduct->id}_".md5($this->service->updated_at);
    }
}
