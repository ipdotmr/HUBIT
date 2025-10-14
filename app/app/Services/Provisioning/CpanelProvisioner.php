<?php

namespace App\Services\Provisioning;

use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CpanelProvisioner implements ProvisionerInterface
{
    private string $whmHost;
    private string $whmToken;
    private bool $useSsl;
    
    public function __construct()
    {
        $this->whmHost = config('services.cpanel.host');
        $this->whmToken = config('services.cpanel.api_token');
        $this->useSsl = config('services.cpanel.use_ssl', true);
    }
    
    public function provision(Service $service): ProvisionResult
    {
        try {
            $product = $service->product;
            $client = $service->client;
            
            $username = $this->generateUsername($client);
            $password = Str::random(16);
            $domain = $this->extractDomain($service);
            $package = $product->provisioner_config['package'] ?? config('services.cpanel.default_package');
            
            $response = $this->makeWhmRequest('createacct', [
                'username' => $username,
                'password' => $password,
                'domain' => $domain,
                'plan' => $package,
                'contactemail' => $client->email,
            ]);
            
            if ($response && isset($response['metadata']['result']) && $response['metadata']['result'] == 1) {
                $service->update([
                    'provision_ref' => [
                        'username' => $username,
                        'domain' => $domain,
                        'package' => $package,
                        'ip' => $response['data']['ip'] ?? null,
                        'nameservers' => [
                            config('services.cpanel.ns1', 'ns1.example.com'),
                            config('services.cpanel.ns2', 'ns2.example.com'),
                        ],
                    ],
                    'credentials' => encrypt([
                        'username' => $username,
                        'password' => $password,
                        'control_panel_url' => $this->getCpanelUrl($domain),
                    ]),
                    'status' => 'active',
                ]);
                
                return ProvisionResult::success('cPanel account created successfully', [
                    'username' => $username,
                    'domain' => $domain,
                ]);
            }
            
            return ProvisionResult::failure($response['metadata']['reason'] ?? 'Failed to create cPanel account');
        } catch (\Exception $e) {
            Log::error('cPanel provision error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionResult::failure($e->getMessage());
        }
    }
    
    public function suspend(Service $service): ProvisionResult
    {
        try {
            $username = $service->provision_ref['username'] ?? null;
            
            if (!$username) {
                return ProvisionResult::failure('Username not found in provision reference');
            }
            
            $response = $this->makeWhmRequest('suspendacct', [
                'user' => $username,
                'reason' => 'Suspended via HUBIT',
            ]);
            
            if ($response && isset($response['metadata']['result']) && $response['metadata']['result'] == 1) {
                $service->update(['status' => 'suspended']);
                return ProvisionResult::success('Account suspended successfully');
            }
            
            return ProvisionResult::failure($response['metadata']['reason'] ?? 'Failed to suspend account');
        } catch (\Exception $e) {
            Log::error('cPanel suspend error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionResult::failure($e->getMessage());
        }
    }
    
    public function unsuspend(Service $service): ProvisionResult
    {
        try {
            $username = $service->provision_ref['username'] ?? null;
            
            if (!$username) {
                return ProvisionResult::failure('Username not found in provision reference');
            }
            
            $response = $this->makeWhmRequest('unsuspendacct', [
                'user' => $username,
            ]);
            
            if ($response && isset($response['metadata']['result']) && $response['metadata']['result'] == 1) {
                $service->update(['status' => 'active']);
                return ProvisionResult::success('Account unsuspended successfully');
            }
            
            return ProvisionResult::failure($response['metadata']['reason'] ?? 'Failed to unsuspend account');
        } catch (\Exception $e) {
            Log::error('cPanel unsuspend error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionResult::failure($e->getMessage());
        }
    }
    
    public function terminate(Service $service): ProvisionResult
    {
        try {
            $username = $service->provision_ref['username'] ?? null;
            
            if (!$username) {
                return ProvisionResult::failure('Username not found in provision reference');
            }
            
            $response = $this->makeWhmRequest('removeacct', [
                'user' => $username,
            ]);
            
            if ($response && isset($response['metadata']['result']) && $response['metadata']['result'] == 1) {
                $service->update(['status' => 'terminated']);
                return ProvisionResult::success('Account terminated successfully');
            }
            
            return ProvisionResult::failure($response['metadata']['reason'] ?? 'Failed to terminate account');
        } catch (\Exception $e) {
            Log::error('cPanel terminate error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionResult::failure($e->getMessage());
        }
    }
    
    public function resetPassword(Service $service, string $password): ProvisionResult
    {
        try {
            $username = $service->provision_ref['username'] ?? null;
            
            if (!$username) {
                return ProvisionResult::failure('Username not found in provision reference');
            }
            
            $response = $this->makeWhmRequest('passwd', [
                'user' => $username,
                'password' => $password,
            ]);
            
            if ($response && isset($response['metadata']['result']) && $response['metadata']['result'] == 1) {
                $credentials = decrypt($service->credentials);
                $credentials['password'] = $password;
                $service->update(['credentials' => encrypt($credentials)]);
                
                return ProvisionResult::success('Password reset successfully');
            }
            
            return ProvisionResult::failure($response['metadata']['reason'] ?? 'Failed to reset password');
        } catch (\Exception $e) {
            Log::error('cPanel password reset error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionResult::failure($e->getMessage());
        }
    }
    
    public function sync(Service $service): ProvisionSync
    {
        try {
            $username = $service->provision_ref['username'] ?? null;
            
            if (!$username) {
                return ProvisionSync::failure('Username not found in provision reference');
            }
            
            $response = $this->makeWhmRequest('getacctstats', [
                'user' => $username,
            ]);
            
            if ($response && isset($response['data'])) {
                $data = $response['data'];
                
                return ProvisionSync::success([
                    'disk_used' => $data['diskused'] ?? 0,
                    'disk_limit' => $data['disklimit'] ?? 0,
                    'bandwidth_used' => $data['bandwidth'] ?? 0,
                ], 'active');
            }
            
            return ProvisionSync::failure('Failed to sync account stats');
        } catch (\Exception $e) {
            Log::error('cPanel sync error', ['error' => $e->getMessage(), 'service_id' => $service->id]);
            return ProvisionSync::failure($e->getMessage());
        }
    }
    
    private function makeWhmRequest(string $function, array $params = []): ?array
    {
        $protocol = $this->useSsl ? 'https' : 'http';
        $url = "{$protocol}://{$this->whmHost}:2087/json-api/{$function}";
        
        $response = Http::withHeaders([
            'Authorization' => 'whm ' . $this->whmToken,
        ])->timeout(30)->get($url, $params);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        Log::error('WHM API request failed', [
            'function' => $function,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        
        return null;
    }
    
    private function generateUsername($client): string
    {
        $base = strtolower(substr($client->first_name . $client->last_name, 0, 8));
        $base = preg_replace('/[^a-z0-9]/', '', $base);
        return $base . rand(100, 999);
    }
    
    private function extractDomain(Service $service): string
    {
        $config = $service->config ?? [];
        return $config['domain'] ?? 'temp' . time() . '.example.com';
    }
    
    private function getCpanelUrl(string $domain): string
    {
        return "https://{$domain}:2083";
    }
}
