<?php

declare(strict_types=1);

use PhpTypedValues\Code\DateTime\DateTimeType;
use PhpTypedValues\Code\Float\FloatType;
use PhpTypedValues\Code\Integer\IntType;
use PhpTypedValues\Code\String\StrType;

arch('Base classes')
    ->expect('PhpTypedValues\Code\BaseType')
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
