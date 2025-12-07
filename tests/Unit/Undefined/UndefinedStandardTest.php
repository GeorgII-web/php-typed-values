<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\UndefinedStandard;

it('creates UndefinedStandard via factory', function (): void {
    $u = UndefinedStandard::create();
    expect($u)->toBeInstanceOf(UndefinedStandard::class);
});

it('throws on toString for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toString())
        ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be converted to string.');
});

it('throws on toInt for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toInt())
        ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be converted to integer.');
});

it('throws on toFloat for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->toFloat())
        ->toThrow(UndefinedTypeException::class, 'Undefined type cannot be converted to float.');
});

it('throws on value for UndefinedStandard', function (): void {
    $u = UndefinedStandard::create();
    expect(fn() => $u->value())
        ->toThrow(UndefinedTypeException::class, 'Undefined type has no value.');
});
