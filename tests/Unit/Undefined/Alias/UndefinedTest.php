<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

it('creates Undefined via factory', function (): void {
    $u = Undefined::create();
    expect($u)->toBeInstanceOf(Undefined::class);
});

it('throws on toString for Undefined', function (): void {
    $u = Undefined::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for Undefined', function (): void {
    $u = Undefined::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
