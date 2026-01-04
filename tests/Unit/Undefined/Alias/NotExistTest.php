<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;

it('creates NotExist via factory', function (): void {
    $u = NotExist::create();
    expect($u)->toBeInstanceOf(NotExist::class);
});

it('throws on toString for NotExist', function (): void {
    $u = NotExist::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for NotExist', function (): void {
    $u = NotExist::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
