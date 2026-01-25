<?php

declare(strict_types=1);

use PhpTypedValues\String\Specific\StringMd5;

describe('Main Architecture', function () {
    arch('strict types')
        ->expect('PhpTypedValues')
        ->toUseStrictTypes();

    arch('no debug')
        ->expect('PhpTypedValues')
        ->not->toUse(['die', 'dd', 'dump']);

    arch()->preset()->php();

    arch()
        ->preset()
        ->security()
        ->ignoring(StringMd5::class); // md5 weak algorithm
});
