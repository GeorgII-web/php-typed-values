<?php

declare(strict_types=1);

use PhpTypedValues\Code\Integer\IntType;

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
