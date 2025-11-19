<?php

declare(strict_types=1);

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
    ->toBeFinal()
    ->toBeReadonly();

arch('Float classes are final and read-only')
    ->expect('PhpTypedValues\Float')
    ->toExtend(FloatType::class)
    ->toBeClasses()
    ->toBeFinal()
    ->toBeReadonly();

arch('String classes are final and read-only')
    ->expect('PhpTypedValues\String')
    ->toExtend(StrType::class)
    ->toBeClasses()
    ->toBeFinal()
    ->toBeReadonly();
