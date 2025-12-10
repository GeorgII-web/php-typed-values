<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

it('FloatStandard::tryFromString returns value on valid float string', function (): void {
    $v = FloatStandard::tryFromString('1.5');

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(1.5)
        ->and($v->toString())
        ->toBe('1.5');
});

it('FloatStandard::tryFromString returns Undefined on invalid float string', function (): void {
    $v = FloatStandard::tryFromString('abc');

    expect($v)->toBeInstanceOf(Undefined::class);
});

it('FloatStandard::tryFromFloat returns value for any int', function (): void {
    $v = FloatStandard::tryFromFloat(2);

    expect($v)
        ->toBeInstanceOf(FloatStandard::class)
        ->and($v->value())
        ->toBe(2.0);
});

it('FloatStandard::fromString throws on non-numeric strings', function (): void {
    expect(fn() => FloatStandard::fromString('NaN'))
        ->toThrow(FloatTypeException::class, 'String "NaN" has no valid float value');
});

it('jsonSerialize returns float', function (): void {
    expect(FloatStandard::tryFromString('1.1')->jsonSerialize())->toBeFloat();
});
