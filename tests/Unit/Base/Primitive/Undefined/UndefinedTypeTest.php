<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

it('__toString throws exception for UndefinedType', function (): void {
    $v = UndefinedStandard::create();

    expect(fn() => (string) $v)
        ->toThrow(UndefinedTypeException::class);
});
