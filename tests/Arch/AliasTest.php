<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Base\Primitive\Float\FloatTypeInterface;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeInterface;
use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Base\Primitive\String\StringTypeInterface;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeInterface;

arch('Alias Boolean')
    ->expect('PhpTypedValues\Bool\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toExtend(BoolTypeAbstract::class)
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
    ->toImplement(IntegerTypeInterface::class)
    ->toBeReadonly();

arch('Alias String')
    ->expect('PhpTypedValues\String\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(StringTypeInterface::class)
    ->toBeReadonly();

arch('Alias Undefined')
    ->expect('PhpTypedValues\Undefined\Alias')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement(PrimitiveTypeInterface::class)
    ->toImplement(UndefinedTypeInterface::class)
    ->toBeReadonly();
