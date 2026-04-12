<?php

declare(strict_types=1);

use App\Rector\Rules\DowngradeStandaloneLiteralParamTypeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../src',
    ])
    ->withRules([
        DowngradeStandaloneLiteralParamTypeRector::class,
    ])
    ->withDowngradeSets(php74: true);
