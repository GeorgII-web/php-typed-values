<?php

declare(strict_types=1);

use PhpTypedValues\Internal\Primitive\Bool\BoolType;
use PhpTypedValues\Internal\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Internal\Primitive\Float\FloatTypeInterface;
use PhpTypedValues\Internal\Primitive\Integer\IntTypeInterface;
use PhpTypedValues\Internal\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Internal\Primitive\String\StrTypeInterface;
use PhpTypedValues\Internal\Primitive\Undefined\UndefinedTypeInterface;

arch('Alias Boolean')
    ->expect('PhpTypedValues\Bool\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toExtend(BoolType::class)
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(BoolTypeInterface::class)
    ->toBeReadonly();

arch('Alias Float')
    ->expect('PhpTypedValues\Float\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(FloatTypeInterface::class)
    ->toBeReadonly();

arch('Alias Integer')
    ->expect('PhpTypedValues\Integer\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(IntTypeInterface::class)
    ->toBeReadonly();

arch('Alias String')
    ->expect('PhpTypedValues\String\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(StrTypeInterface::class)
    ->toBeReadonly();

arch('Alias Undefined')
    ->expect('PhpTypedValues\Undefined\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(UndefinedTypeInterface::class)
    ->toBeReadonly();
