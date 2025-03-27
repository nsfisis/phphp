<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

require_once __DIR__ . '/vendor/autoload.php';

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/index.php',
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
    );
