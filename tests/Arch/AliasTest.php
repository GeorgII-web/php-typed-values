<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\AbstractTypeInterface;
use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Abstract\Bool\BoolTypeInterface;
use PhpTypedValues\Abstract\Float\FloatTypeInterface;
use PhpTypedValues\Abstract\Integer\IntTypeInterface;
use PhpTypedValues\Abstract\String\StrTypeInterface;
use PhpTypedValues\Abstract\Undefined\UndefinedTypeInterface;

arch('Alias Boolean')
    ->expect('PhpTypedValues\Bool\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toExtend(BoolType::class)
    ->toImplement(AbstractTypeInterface::class)
    ->toImplement(BoolTypeInterface::class)
    ->toBeReadonly();

arch('Alias Float')
    ->expect('PhpTypedValues\Float\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(AbstractTypeInterface::class)
    ->toImplement(FloatTypeInterface::class)
    ->toBeReadonly();

arch('Alias Integer')
    ->expect('PhpTypedValues\Integer\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(AbstractTypeInterface::class)
    ->toImplement(IntTypeInterface::class)
    ->toBeReadonly();

arch('Alias String')
    ->expect('PhpTypedValues\String\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(AbstractTypeInterface::class)
    ->toImplement(StrTypeInterface::class)
    ->toBeReadonly();

arch('Alias Undefined')
    ->expect('PhpTypedValues\Undefined\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(AbstractTypeInterface::class)
    ->toImplement(UndefinedTypeInterface::class)
    ->toBeReadonly();
