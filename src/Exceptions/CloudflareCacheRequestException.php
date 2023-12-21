<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache\Exceptions;

use RuntimeException;

class CloudflareCacheRequestException extends RuntimeException
{
    public static function requestError(int $status, string $message, ?int $code): self
    {
        return new static('Request error: ' . $message . ' | Code: ' . $code, $status); // @phpstan-ignore
    }
}
