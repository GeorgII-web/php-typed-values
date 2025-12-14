<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\Primitive\Bool\BoolType;
use PhpTypedValues\Abstract\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Abstract\Primitive\Float\FloatTypeInterface;
use PhpTypedValues\Abstract\Primitive\Integer\IntTypeInterface;
use PhpTypedValues\Abstract\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Abstract\Primitive\String\StrTypeInterface;
use PhpTypedValues\Abstract\Primitive\Undefined\UndefinedTypeInterface;

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
