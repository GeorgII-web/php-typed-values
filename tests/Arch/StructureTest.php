<?php

declare(strict_types=1);

use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Internal\Primitive\Bool\BoolType;
use PhpTypedValues\Internal\Primitive\DateTime\DateTimeType;
use PhpTypedValues\Internal\Primitive\Float\FloatType;
use PhpTypedValues\Internal\Primitive\Integer\IntType;
use PhpTypedValues\Internal\Primitive\String\StrType;
use PhpTypedValues\Internal\Primitive\Undefined\UndefinedType;
use PhpTypedValues\Undefined\UndefinedStandard;

arch('Abstract Int classes')
    ->expect(IntType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Integer classes are final and read-only')
    ->expect('PhpTypedValues\Integer')
    ->toExtend(IntType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Abstract Float classes')
    ->expect(FloatType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Float classes are final and read-only')
    ->expect('PhpTypedValues\Float')
    ->toExtend(FloatType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Abstract Str classes')
    ->expect(StrType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('String classes are final and read-only')
    ->expect('PhpTypedValues\String')
    ->toExtend(StrType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Abstract Datetime classes')
    ->expect(DateTimeType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Datetime classes are final and read-only')
    ->expect('PhpTypedValues\DateTime')
    ->toExtend(DateTimeType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Abstract Bool classes')
    ->expect(BoolType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Boolean classes are final and read-only')
    ->expect('PhpTypedValues\Bool')
    ->toExtend(BoolType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Abstract Undefined classes')
    ->expect(UndefinedType::class)
    ->toBeClasses()
    ->toBeAbstract()
    ->toBeReadonly()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Undefined classes are read-only and extend UndefinedType')
    ->expect('PhpTypedValues\Undefined')
    ->toExtend(UndefinedType::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('Undefined aliases extend UndefinedStandard and are read-only')
    ->expect('PhpTypedValues\Undefined\Alias')
    ->toExtend(UndefinedStandard::class)
    ->toBeClasses()
    ->toBeReadonly();

arch('All exceptions extend the base TypeException')
    ->expect('PhpTypedValues\Exception')
    ->toExtend(TypeException::class)
    ->toBeClasses();
