<?php

namespace App\Jobs\Service;

use App\Models\Service;
use App\Services\ProvisioningLogService;
use App\Services\Testing\MockFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 120, 300];

    public function __construct(
        public Service $service,
        public ?string $action = null
    ) {
    }

    public function handle(ProvisioningLogService $logger): void
    {
        $logger->start('service.sync', $this->service, [
            'action' => $this->action,
            'idempotency_key' => $this->getIdempotencyKey(),
        ]);

        try {
            $provisioner = $this->getProvisioner();

            if ($this->action === 'suspend') {
                $result = $provisioner->suspend($this->service);
                if ($result->success) {
                    $this->service->update(['status' => 'suspended']);
                }
            } elseif ($this->action === 'unsuspend') {
                $result = $provisioner->unsuspend($this->service);
                if ($result->success) {
                    $this->service->update(['status' => 'active']);
                }
            } else {
                $result = $provisioner->sync($this->service);
            }

            if ($result->success) {
                if ($result->sync) {
                    $this->service->update([
                        'provision_ref' => array_merge(
                            $this->service->provision_ref ?? [],
                            $result->sync->toArray()
                        ),
                    ]);
                }

                $logger->success('service.sync', $this->service, [
                    'action' => $this->action,
                    'result' => $result->toArray(),
                ]);
            } else {
                throw new \Exception($result->error ?? 'Sync failed');
            }
        } catch (\Exception $e) {
            $logger->failure('service.sync', $this->service, $e);
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
        return "sync_{$this->service->id}_{$this->action}_".md5($this->service->updated_at);
    }
}
