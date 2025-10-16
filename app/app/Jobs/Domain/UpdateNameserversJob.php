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

class UpdateNameserversJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(
        public Domain $domain,
        public array $nameservers
    ) {
    }

    public function handle(ProvisioningLogService $logger): void
    {
        $idempotencyKey = $this->getIdempotencyKey();

        $logger->start('domain.nameservers.update', $this->domain, [
            'nameservers' => $this->nameservers,
            'idempotency_key' => $idempotencyKey,
        ]);

        try {
            $registrar = $this->getRegistrarClient();

            $result = $registrar->updateNameservers($this->domain->domain, $this->nameservers);

            if ($result['success']) {
                $this->domain->update(['nameservers' => $this->nameservers]);

                $logger->success('domain.nameservers.update', $this->domain, [
                    'nameservers' => $this->nameservers,
                    'result' => $result,
                ]);
            } else {
                throw new \Exception($result['error'] ?? 'Failed to update nameservers');
            }
        } catch (\Exception $e) {
            $logger->failed('domain.nameservers.update', $this->domain, [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                Log::error('Nameserver update failed permanently', [
                    'domain_id' => $this->domain->id,
                    'nameservers' => $this->nameservers,
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
        return 'ns_'.$this->domain->id.'_'.md5(json_encode($this->nameservers));
    }

    public function tags(): array
    {
        return ['registrar:'.$this->domain->registrar, 'domain:'.$this->domain->id];
    }
}
