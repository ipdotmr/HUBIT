<?php

namespace App\Contracts;

use App\Models\Domain;
use App\Models\DomainOrder;

interface RegistrarInterface
{
    /**
     * Check if a domain is available for registration
     *
     * @param  string  $domain  Domain name to check
     * @return array ['available' => bool, 'price' => float|null, 'premium' => bool]
     */
    public function checkAvailability(string $domain): array;

    /**
     * Register a new domain
     *
     * @param  DomainOrder  $order  Domain order details
     * @return array ['success' => bool, 'domain_id' => string|null, 'message' => string]
     */
    public function register(DomainOrder $order): array;

    /**
     * Renew an existing domain
     *
     * @param  Domain  $domain  Domain to renew
     * @param  int  $years  Number of years to renew
     * @return array ['success' => bool, 'expiry_date' => string|null, 'message' => string]
     */
    public function renew(Domain $domain, int $years = 1): array;

    /**
     * Transfer a domain to this registrar
     *
     * @param  Domain  $domain  Domain to transfer
     * @param  string  $authCode  EPP/authorization code
     * @return array ['success' => bool, 'transfer_id' => string|null, 'message' => string]
     */
    public function transfer(Domain $domain, string $authCode): array;

    /**
     * Set nameservers for a domain
     *
     * @param  Domain  $domain  Domain to update
     * @param  array  $nameservers  Array of nameserver hostnames
     * @return array ['success' => bool, 'message' => string]
     */
    public function setNameservers(Domain $domain, array $nameservers): array;

    /**
     * Get the authorization/EPP code for a domain
     *
     * @param  Domain  $domain  Domain to get auth code for
     * @return array ['success' => bool, 'auth_code' => string|null, 'message' => string]
     */
    public function getAuthCode(Domain $domain): array;

    /**
     * Lock or unlock a domain
     *
     * @param  Domain  $domain  Domain to lock/unlock
     * @param  bool  $locked  True to lock, false to unlock
     * @return array ['success' => bool, 'message' => string]
     */
    public function setLock(Domain $domain, bool $locked): array;

    /**
     * Enable or disable WHOIS privacy
     *
     * @param  Domain  $domain  Domain to update privacy for
     * @param  bool  $enabled  True to enable privacy, false to disable
     * @return array ['success' => bool, 'message' => string]
     */
    public function setPrivacy(Domain $domain, bool $enabled): array;

    /**
     * Get WHOIS information for a domain
     *
     * @param  Domain  $domain  Domain to query
     * @param  string  $language  Language code (en, ar, fr)
     * @return array ['success' => bool, 'whois' => array|null, 'message' => string]
     */
    public function getWhois(Domain $domain, string $language = 'en'): array;

    /**
     * Sync domain details from registrar
     *
     * @param  Domain  $domain  Domain to sync
     * @return array ['success' => bool, 'data' => array|null, 'message' => string]
     */
    public function sync(Domain $domain): array;

    /**
     * Test connection to registrar API
     *
     * @return array ['success' => bool, 'latency' => int|null, 'message' => string, 'details' => array]
     */
    public function testConnection(): array;
}
