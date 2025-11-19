<?php

declare(strict_types=1);

use PhpTypedValues\Code\BaseType\BaseIntType;

arch('Base classes')
    ->expect('PhpTypedValues\Code\BaseType')
    ->toBeClasses()
    ->toBeAbstract()
    ->toOnlyBeUsedIn('PhpTypedValues');

arch('Integer classes are final and read-only')
    ->expect('PhpTypedValues\Integer')
    ->toExtend(BaseIntType::class)
    ->toBeClasses()
    ->toBeFinal()
    ->toBeReadonly();
