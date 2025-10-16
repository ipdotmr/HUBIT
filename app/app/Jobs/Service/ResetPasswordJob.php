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

class ResetPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 30, 60];

    public function __construct(
        public Service $service,
        public string $password
    ) {}

    public function handle(ProvisioningLogService $logger): void
    {
        $logger->start('service.password_reset', $this->service, [
            'idempotency_key' => $this->getIdempotencyKey(),
        ]);

        try {
            $provisioner = $this->getProvisioner();

            $result = $provisioner->resetPassword($this->service, $this->password);

            if ($result->success) {
                $logger->success('service.password_reset', $this->service, [
                    'result' => $result->toArray(),
                ]);
            } else {
                throw new \Exception($result->error ?? 'Password reset failed');
            }
        } catch (\Exception $e) {
            $logger->failure('service.password_reset', $this->service, $e);
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
        return "reset_pwd_{$this->service->id}_".md5($this->password.$this->service->updated_at);
    }
}
