<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DowngradePhp82\Rector\FunctionLike\DowngradeStandaloneNullTrueFalseReturnTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../src',
    ])
    ->withRules([
        DowngradeStandaloneNullTrueFalseReturnTypeRector::class,
    ])
    ->withDowngradeSets(php74: true);
