<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Unknown;

it('creates Unknown via factory', function (): void {
    $u = Unknown::create();
    expect($u)->toBeInstanceOf(Unknown::class);
});

it('throws on toString for Unknown', function (): void {
    $u = Unknown::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be converted to string.');
});

it('throws on value for Unknown', function (): void {
    $u = Unknown::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'Undefined type has no value.');
});
