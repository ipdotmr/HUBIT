<?php

namespace App\Jobs\Domain;

use App\Models\Domain;
use App\Models\DomainRenewal;
use App\Services\ProvisioningLogService;
use App\Services\Testing\MockFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RenewDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(
        public DomainRenewal $renewal
    ) {}

    public function handle(ProvisioningLogService $logger): void
    {
        $domain = $this->renewal->domain;
        $idempotencyKey = $this->getIdempotencyKey();

        $logger->start('domain.renewal', $domain, [
            'renewal_id' => $this->renewal->id,
            'years' => $this->renewal->years,
            'idempotency_key' => $idempotencyKey,
        ]);

        try {
            $registrar = $this->getRegistrarClient($domain);

            $result = $registrar->renewDomain($domain->domain, $this->renewal->years);

            if ($result['success']) {
                $domain->update(['expires_at' => $this->renewal->new_expiry]);

                $this->renewal->update(['status' => 'completed']);

                $logger->success('domain.renewal', $domain, [
                    'renewal_id' => $this->renewal->id,
                    'new_expiry' => $this->renewal->new_expiry,
                    'result' => $result,
                ]);
            } else {
                throw new \Exception($result['error'] ?? 'Failed to renew domain');
            }
        } catch (\Exception $e) {
            $logger->failed('domain.renewal', $domain, [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->renewal->update(['status' => 'failed']);

                Log::error('Domain renewal failed permanently', [
                    'renewal_id' => $this->renewal->id,
                    'domain_id' => $domain->id,
                    'error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    protected function getRegistrarClient(Domain $domain)
    {
        if (MockFactory::shouldUseMocks()) {
            return MockFactory::registrar($domain->registrar);
        }

        return app('App\Services\Registrars\\'.ucfirst($domain->registrar).'Registrar');
    }

    protected function getIdempotencyKey(): string
    {
        return 'renew_'.$this->renewal->id.'_'.$this->renewal->years;
    }

    public function tags(): array
    {
        $domain = $this->renewal->domain;

        return ['registrar:'.$domain->registrar, 'domain:'.$domain->id, 'renewal:'.$this->renewal->id];
    }
}
