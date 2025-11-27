<?php

declare(strict_types=1);

use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Abstract\DateTime\DateTimeType;
use PhpTypedValues\Abstract\Float\FloatType;
use PhpTypedValues\Abstract\Integer\IntType;
use PhpTypedValues\Abstract\String\StrType;

arch('Base classes')
    ->expect('PhpTypedValues\Abstract\BaseType')
    ->toBeClasses()
    ->toBeAbstract()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Integer classes are final and read-only')
    ->expect('PhpTypedValues\Integer')
    ->toExtend(IntType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Float classes are final and read-only')
    ->expect('PhpTypedValues\Float')
    ->toExtend(FloatType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('String classes are final and read-only')
    ->expect('PhpTypedValues\String')
    ->toExtend(StrType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Datetime classes are final and read-only')
    ->expect('PhpTypedValues\DateTime')
    ->toExtend(DateTimeType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Boolean classes are final and read-only')
    ->expect('PhpTypedValues\Bool')
    ->toExtend(BoolType::class)
    ->toBeClasses()
    ->toBeReadonly();
