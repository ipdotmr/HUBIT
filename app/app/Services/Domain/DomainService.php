<?php

namespace App\Services\Domain;

use App\Models\Domain;
use App\Models\DomainOrder;
use App\Contracts\RegistrarInterface;
use App\Services\Registrars\NamecheapRegistrar;
use App\Services\Registrars\NamecomRegistrar;
use App\Services\Registrars\CoccaepRegistrar;
use Illuminate\Support\Facades\Log;

class DomainService
{
    protected array $registrars = [];

    public function __construct()
    {
        // Initialize available registrars
        $this->registerRegistrar('namecheap', app(NamecheapRegistrar::class));
        $this->registerRegistrar('namecom', app(NamecomRegistrar::class));
        $this->registerRegistrar('coccaep', app(CoccaepRegistrar::class));
    }

    public function registerRegistrar(string $name, RegistrarInterface $registrar): void
    {
        $this->registrars[$name] = $registrar;
    }

    public function getRegistrar(string $name): ?RegistrarInterface
    {
        return $this->registrars[$name] ?? null;
    }

    public function search(string $fqdn, ?string $registrarName = null): array
    {
        $registrar = $registrarName 
            ? $this->getRegistrar($registrarName)
            : $this->registrars[array_key_first($this->registrars)] ?? null;

        if (!$registrar) {
            return [
                'available' => false,
                'price' => null,
                'error' => 'No registrar configured',
            ];
        }

        try {
            $result = $registrar->checkAvailability($fqdn);
            
            return [
                'available' => $result['available'] ?? false,
                'price' => $result['price'] ?? null,
                'registrar' => $registrarName,
                'suggestions' => $this->getSuggestions($fqdn, $registrar),
            ];
        } catch (\Exception $e) {
            Log::error('Domain search failed', [
                'fqdn' => $fqdn,
                'registrar' => $registrarName,
                'error' => $e->getMessage(),
            ]);

            return [
                'available' => false,
                'price' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function register(DomainOrder $order): array
    {
        $registrar = $this->getRegistrar($order->registrar);

        if (!$registrar) {
            throw new \Exception("Registrar {$order->registrar} not configured");
        }

        try {
            $result = $registrar->registerDomain([
                'domain' => $order->fqdn,
                'years' => $order->years,
                'contacts' => $order->contacts ?? [],
                'nameservers' => $order->nameservers ?? [],
                'privacy' => $order->privacy ?? false,
            ]);

            $domain = Domain::create([
                'client_id' => $order->client_id,
                'fqdn' => $order->fqdn,
                'registrar' => $order->registrar,
                'status' => 'active',
                'registered_at' => now(),
                'expires_at' => now()->addYears($order->years),
            ]);

            $order->update([
                'status' => 'completed',
                'domain_id' => $domain->id,
            ]);

            return [
                'success' => true,
                'domain' => $domain,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Domain registration failed', [
                'order_id' => $order->id,
                'fqdn' => $order->fqdn,
                'error' => $e->getMessage(),
            ]);

            $order->update(['status' => 'failed']);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function renew(Domain $domain, int $years): array
    {
        $registrar = $this->getRegistrar($domain->registrar);

        if (!$registrar) {
            throw new \Exception("Registrar {$domain->registrar} not configured");
        }

        try {
            $result = $registrar->renewDomain($domain->fqdn, $years);

            $domain->update([
                'expires_at' => $domain->expires_at->addYears($years),
            ]);

            return [
                'success' => true,
                'domain' => $domain->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Domain renewal failed', [
                'domain_id' => $domain->id,
                'fqdn' => $domain->fqdn,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function transfer(string $fqdn, string $authCode, string $registrarName): array
    {
        $registrar = $this->getRegistrar($registrarName);

        if (!$registrar) {
            throw new \Exception("Registrar {$registrarName} not configured");
        }

        try {
            $result = $registrar->transferDomain($fqdn, $authCode);

            return [
                'success' => true,
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Domain transfer failed', [
                'fqdn' => $fqdn,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function setNameservers(Domain $domain, array $nameservers): array
    {
        $registrar = $this->getRegistrar($domain->registrar);

        if (!$registrar) {
            throw new \Exception("Registrar {$domain->registrar} not configured");
        }

        try {
            $result = $registrar->updateNameservers($domain->fqdn, $nameservers);

            $domain->update(['nameservers' => $nameservers]);

            return [
                'success' => true,
                'domain' => $domain->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Nameserver update failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function togglePrivacy(Domain $domain, bool $enabled): array
    {
        $registrar = $this->getRegistrar($domain->registrar);

        if (!$registrar) {
            throw new \Exception("Registrar {$domain->registrar} not configured");
        }

        try {
            $result = $registrar->togglePrivacy($domain->fqdn, $enabled);

            $domain->update(['privacy' => $enabled]);

            return [
                'success' => true,
                'domain' => $domain->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Privacy toggle failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function toggleLock(Domain $domain, bool $locked): array
    {
        $registrar = $this->getRegistrar($domain->registrar);

        if (!$registrar) {
            throw new \Exception("Registrar {$domain->registrar} not configured");
        }

        try {
            $result = $registrar->setLock($domain->fqdn, $locked);

            $domain->update(['locked' => $locked]);

            return [
                'success' => true,
                'domain' => $domain->fresh(),
                'result' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Lock toggle failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function getSuggestions(string $fqdn, RegistrarInterface $registrar): array
    {
        // Extract domain name without TLD
        $parts = explode('.', $fqdn);
        $name = $parts[0];
        
        // Common TLD suggestions
        $tlds = ['.com', '.net', '.org', '.io', '.co'];
        $suggestions = [];

        foreach ($tlds as $tld) {
            $suggested = $name . $tld;
            if ($suggested !== $fqdn) {
                try {
                    $result = $registrar->checkAvailability($suggested);
                    if ($result['available'] ?? false) {
                        $suggestions[] = [
                            'fqdn' => $suggested,
                            'price' => $result['price'] ?? null,
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip failed suggestions
                }
            }
        }

        return $suggestions;
    }
}
