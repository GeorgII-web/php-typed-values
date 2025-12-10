<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotSet;

it('creates NotSet via factory', function (): void {
    $u = NotSet::create();
    expect($u)->toBeInstanceOf(NotSet::class);
});

it('throws on toString for NotSet', function (): void {
    $u = NotSet::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType cannot be converted to string.');
});

it('throws on value for NotSet', function (): void {
    $u = NotSet::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'UndefinedType has no value.');
});
