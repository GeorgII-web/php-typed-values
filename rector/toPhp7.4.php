<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    // We will process code in this specific directory during CI
    $rectorConfig->paths([
        __DIR__,
    ]);

    // Downgrade rules to make code compatible with PHP 7.2
    // This handles readonly classes, typed properties, union types, etc.
    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_74,
    ]);
};
