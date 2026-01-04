<?php

declare(strict_types=1);

use PhpTypedValues\Integer\String\Specific\StringMd5;

arch('strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('no debug')
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump']);

arch()->preset()->php();

arch()
    ->preset()
    ->security()
    ->ignoring(StringMd5::class); // md5 weak algorithm
