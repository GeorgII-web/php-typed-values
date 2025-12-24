<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NoValue;

it('creates NoValue via factory', function (): void {
    $u = NoValue::create();
    expect($u)->toBeInstanceOf(NoValue::class);
});

it('throws on toString for NoValue', function (): void {
    $u = NoValue::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for NoValue', function (): void {
    $u = NoValue::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
