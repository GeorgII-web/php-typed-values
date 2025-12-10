<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotFound;

it('creates NotFound via factory', function (): void {
    $u = NotFound::create();
    expect($u)->toBeInstanceOf(NotFound::class);
});

it('throws on toString for NotFound', function (): void {
    $u = NotFound::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for NotFound', function (): void {
    $u = NotFound::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
