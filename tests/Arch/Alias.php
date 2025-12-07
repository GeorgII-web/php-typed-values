<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Abstract\Bool\BoolTypeInterface;
use PhpTypedValues\Abstract\TypeInterface;

arch('Alias Boolean')
    ->expect('PhpTypedValues\Bool\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toExtend(BoolType::class)
    ->toImplement(TypeInterface::class)
    ->toImplement(BoolTypeInterface::class)
    ->toUseNothing()
    ->toBeReadonly();
