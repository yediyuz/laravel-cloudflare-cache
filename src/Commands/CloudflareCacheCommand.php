<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache\Commands;

use Illuminate\Console\Command;

class CloudflareCacheCommand extends Command
{
    public $signature = 'cloudflare-cache';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
