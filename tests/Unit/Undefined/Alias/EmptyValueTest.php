<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\EmptyValue;

it('creates EmptyValue via factory', function (): void {
    $u = EmptyValue::create();
    expect($u)->toBeInstanceOf(EmptyValue::class);
});

it('throws on toString for EmptyValue', function (): void {
    $u = EmptyValue::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for EmptyValue', function (): void {
    $u = EmptyValue::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
