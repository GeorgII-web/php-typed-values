<?php

declare(strict_types=1);

use PhpTypedValues\BaseType\BaseIntType;

arch('Base classes')
    ->expect('PhpTypedValues\BaseType')
    ->toBeClasses()
    ->toBeAbstract()
    ->toOnlyBeUsedIn('PhpTypedValues\Type');

arch('Integer classes are final and read-only')
    ->expect('PhpTypedValues\Type\Integer')
    ->toExtend(BaseIntType::class)
    ->toBeClasses()
    ->toBeFinal()
    ->toBeReadonly();
