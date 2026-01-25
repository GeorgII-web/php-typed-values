<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\DateTime\DateTimeTypeAbstract;
use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract;
use PhpTypedValues\Base\Primitive\Integer\IntegerTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeAbstract;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

describe('Structure', function () {
    arch('Abstract Int classes')
        ->expect(IntegerTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('Integer classes are final and read-only')
        ->expect('PhpTypedValues\Integer')
        ->toExtend(IntegerTypeAbstract::class)
        ->toBeClasses()
        ->toBeReadonly();

    arch('Abstract Float classes')
        ->expect(FloatTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('Float classes are final and read-only')
        ->expect('PhpTypedValues\Float')
        ->toExtend(FloatTypeAbstract::class)
        ->toBeClasses()
        ->toBeReadonly();

    arch('Abstract Str classes')
        ->expect(StringTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('String classes are final and read-only')
        ->expect('PhpTypedValues\String')
        ->toExtend(StringTypeAbstract::class)
        ->toBeClasses()
        ->toBeReadonly();

    arch('Abstract Datetime classes')
        ->expect(DateTimeTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('Datetime classes are final and read-only')
        ->expect('PhpTypedValues\DateTime')
        ->toExtend(DateTimeTypeAbstract::class)
        ->toBeClasses()
        ->toBeReadonly();

    arch('Abstract Bool classes')
        ->expect(BoolTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('Boolean classes are final and read-only')
        ->expect('PhpTypedValues\Bool')
        ->toExtend(BoolTypeAbstract::class)
        ->toBeClasses()
        ->toBeReadonly();

    arch('Abstract Undefined classes')
        ->expect(UndefinedTypeAbstract::class)
        ->toBeClasses()
        ->toBeAbstract()
        ->toBeReadonly()
        ->toOnlyBeUsedIn('PhpTypedValues');

    arch('Undefined classes are read-only and extend UndefinedType')
        ->expect('PhpTypedValues\Undefined')
        ->toExtend(UndefinedTypeAbstract::class)
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
});
