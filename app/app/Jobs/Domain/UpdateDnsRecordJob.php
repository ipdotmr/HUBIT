<?php

namespace App\Jobs\Domain;

use App\Models\Domain;
use App\Models\DomainDnsRecord;
use App\Services\ProvisioningLogService;
use App\Services\Testing\MockFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateDnsRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public function __construct(
        public Domain $domain,
        public DomainDnsRecord $record,
        public string $operation
    ) {
    }

    public function handle(ProvisioningLogService $logger): void
    {
        $idempotencyKey = $this->getIdempotencyKey();

        $logger->start("domain.dns.{$this->operation}", $this->domain, [
            'record' => $this->record->toArray(),
            'operation' => $this->operation,
            'idempotency_key' => $idempotencyKey,
        ]);

        try {
            $registrar = $this->getRegistrarClient();

            $result = match ($this->operation) {
                'create' => $registrar->createDnsRecord($this->domain->domain, $this->record->toArray()),
                'update' => $registrar->updateDnsRecord($this->domain->domain, $this->record->toArray()),
                'delete' => $registrar->deleteDnsRecord($this->domain->domain, $this->record->toArray()),
                default => throw new \InvalidArgumentException("Invalid operation: {$this->operation}"),
            };

            if ($result['success']) {
                $logger->success("domain.dns.{$this->operation}", $this->domain, [
                    'record' => $this->record->toArray(),
                    'result' => $result,
                ]);
            } else {
                throw new \Exception($result['error'] ?? "Failed to {$this->operation} DNS record");
            }
        } catch (\Exception $e) {
            $logger->failed("domain.dns.{$this->operation}", $this->domain, [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            if ($this->attempts() >= $this->tries) {
                Log::error("DNS record {$this->operation} failed permanently", [
                    'domain_id' => $this->domain->id,
                    'record' => $this->record->toArray(),
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
        return "dns_{$this->operation}_{$this->record->id}_".md5(json_encode($this->record->toArray()));
    }

    public function tags(): array
    {
        return ['registrar:'.$this->domain->registrar, 'domain:'.$this->domain->id, 'dns:'.$this->operation];
    }
}
