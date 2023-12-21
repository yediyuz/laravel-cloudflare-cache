<?php

declare(strict_types=1);

use Yediyuz\DevTools\PhpCsFixer;

return PhpCsFixer::laravelPackage(__DIR__)/** <hasConfigElse> */
                 ->excludeDir('config')/** </hasConfigElse> */
                 ->build();
