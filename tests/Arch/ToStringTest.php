<?php

declare(strict_types=1);

describe('ToString', function () {
    arch('all VO interfaces declare __toString()')
        ->expect([
            'PhpTypedValues\Abstract\Integer\IntTypeInterface.php',
            'PhpTypedValues\Abstract\Float\FloatTypeInterface.php',
            'PhpTypedValues\Abstract\String\StrTypeInterface.php',
            'PhpTypedValues\Abstract\DateTime\DateTimeTypeInterface.php',
            'PhpTypedValues\Abstract\Bool\BoolTypeInterface.php',
            'PhpTypedValues\Abstract\Undefined\UndefinedTypeInterface.php',
        ])
        ->toBeInterfaces()
        ->toHaveMethod('__toString');

    arch('all value objects have __toString() implementation')
        ->expect([
            'PhpTypedValues\Integer\IntType.php',
            'PhpTypedValues\Float\FloatType.php',
            'PhpTypedValues\String\StrType.php',
            'PhpTypedValues\DateTime\DateTimeType.php',
            'PhpTypedValues\Bool\BoolType.php',
            'PhpTypedValues\Undefined\UndefinedType.php',
        ])
        ->toBeClasses()
        ->toHaveMethod('__toString');
});
