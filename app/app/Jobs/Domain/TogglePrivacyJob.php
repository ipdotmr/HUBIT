<?php

namespace App\Jobs\Domain;

use App\Models\Domain;
use App\Services\ProvisioningLogService;
use App\Services\Testing\MockFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TogglePrivacyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(
        public Domain $domain,
        public bool $enabled
    ) {}

    public function handle(ProvisioningLogService $logger): void
    {
        $idempotencyKey = $this->getIdempotencyKey();

        $logger->start('domain.privacy.toggle', $this->domain, [
            'enabled' => $this->enabled,
            'idempotency_key' => $idempotencyKey,
        ]);

        try {
            $registrar = $this->getRegistrarClient();

            $result = $registrar->togglePrivacy($this->domain->domain, $this->enabled);

            if ($result['success']) {
                $this->domain->update(['privacy_enabled' => $this->enabled]);

                $logger->success('domain.privacy.toggle', $this->domain, [
                    'enabled' => $this->enabled,
                    'result' => $result,
                ]);
            } else {
                throw new \Exception($result['error'] ?? 'Failed to toggle privacy');
            }
        } catch (\Exception $e) {
            $logger->failed('domain.privacy.toggle', $this->domain, [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                Log::error('Privacy toggle failed permanently', [
                    'domain_id' => $this->domain->id,
                    'enabled' => $this->enabled,
                    'error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    protected function getRegistrarClient()
    {
        if (MockFactory::shouldUseMocks()) {
            return MockFactory::registrar($this->domain->registrar);
        }

        return app('App\Services\Registrars\\'.ucfirst($this->domain->registrar).'Registrar');
    }

    protected function getIdempotencyKey(): string
    {
        return 'privacy_'.$this->domain->id.'_'.($this->enabled ? '1' : '0');
    }

    public function tags(): array
    {
        return ['registrar:'.$this->domain->registrar, 'domain:'.$this->domain->id];
    }
}
