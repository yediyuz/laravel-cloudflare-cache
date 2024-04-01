<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache\Services;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

class CloudflareService implements CloudflareServiceInterface
{
    public function __construct(
        private readonly Factory $client,
        private readonly ?string $apiEmail,
        private readonly ?string $apiKey,
        private readonly ?string $identifier,
        private readonly ?string $apiToken,
    ) {
        // .
    }

    private function request(): PendingRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->client->withHeaders($this->apiToken
            ? ['Authorization' => 'Bearer ' . $this->apiToken]
            : ['X-Auth-Email' => $this->apiEmail, 'X-Auth-Key' => $this->apiKey]
        );
    }

    protected function getBaseUrl(string $endpoint): string
    {
        return 'https://api.cloudflare.com/client/v4/zones/' . $this->identifier . '/' . ltrim($endpoint, '/');
    }

    /**
     * @param string[]|array<string, bool>|string[][] $data
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->request()->post($this->getBaseUrl($endpoint), $data);
    }

    /**
     * @param array<int, string>|string|null $query
     */
    public function get(string $endpoint, array|string $query = null): Response
    {
        return $this->request()->get($this->getBaseUrl($endpoint), $query);
    }
}
