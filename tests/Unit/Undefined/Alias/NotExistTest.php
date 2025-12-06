<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;

it('creates NotExist via factory', function (): void {
    $u = NotExist::create();
    expect($u)->toBeInstanceOf(NotExist::class);
});

it('throws on toString for NotExist', function (): void {
    $u = NotExist::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be converted to string.');
});

it('throws on value for NotExist', function (): void {
    $u = NotExist::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'Undefined type has no value.');
});
