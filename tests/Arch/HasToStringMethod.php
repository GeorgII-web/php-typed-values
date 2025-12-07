<?php

declare(strict_types=1);

arch('all VO interfaces declare __toString()')
    ->expect([
        'PhpTypedValues\Abstract\Integer',
        'PhpTypedValues\Abstract\Float',
        'PhpTypedValues\Abstract\String',
        'PhpTypedValues\Abstract\DateTime',
        'PhpTypedValues\Abstract\Bool',
        'PhpTypedValues\Abstract\Undefined',
    ])
    ->toBeInterfaces()
    ->toHaveMethod('__toString');

arch('all value objects have __toString() implementation')
    ->expect([
        'PhpTypedValues\Integer',
        'PhpTypedValues\Float',
        'PhpTypedValues\String',
        'PhpTypedValues\DateTime',
        'PhpTypedValues\Bool',
        'PhpTypedValues\Undefined',
    ])
    ->toBeClasses()
    ->toHaveMethod('__toString');
