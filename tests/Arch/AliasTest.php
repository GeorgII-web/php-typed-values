<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Base\Primitive\Float\FloatTypeInterface;
use PhpTypedValues\Base\Primitive\Integer\IntTypeInterface;
use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Base\Primitive\String\StrTypeInterface;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeInterface;

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
