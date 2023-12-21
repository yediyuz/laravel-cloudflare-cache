<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache\Services;

use Illuminate\Http\Client\Response;
interface CloudflareServiceInterface
{
    /**
     * @param string[]|array<string, bool>|string[][] $data
     */
    public function post(string $endpoint, array $data = []): Response;

    /**
     * @param array<int, string>|string|null $query
     */
    public function get(string $endpoint, array|string $query = null): Response;
}
