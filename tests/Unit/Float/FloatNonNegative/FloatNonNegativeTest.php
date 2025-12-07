<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

it('FloatNonNegative::tryFromString returns value for >= 0.0 and Undefined otherwise', function (): void {
    $ok0 = FloatNonNegative::tryFromString('0');
    $ok = FloatNonNegative::tryFromString('0.5');
    $bad = FloatNonNegative::tryFromString('-0.1');
    $badStr = FloatNonNegative::tryFromString('abc');

    expect($ok0)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok0->value())->toBe(0.0)
        ->and($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())->toBe(0.5)
        ->and($bad)->toBeInstanceOf(Undefined::class)
        ->and($badStr)->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative::tryFromFloat returns value for >= 0 and Undefined otherwise', function (): void {
    $ok = FloatNonNegative::tryFromFloat(0);
    $bad = FloatNonNegative::tryFromFloat(-1);

    expect($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())
        ->toBe(0.0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative throws on negative values in ctor and fromFloat', function (): void {
    expect(fn() => new FloatNonNegative(-0.1))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.1"')
        ->and(fn() => FloatNonNegative::fromFloat(-1.0))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-1"');
});

it('FloatNonNegative::fromString enforces numeric and non-negativity', function (): void {
    // Non-numeric
    expect(fn() => FloatNonNegative::fromString('abc'))
        ->toThrow(FloatTypeException::class, 'String "abc" has no valid float value');

    // Non-negativity
    expect(fn() => FloatNonNegative::fromString('-0.5'))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.5"');

    // Success path
    $v = FloatNonNegative::fromString('0.75');
    expect($v->value())->toBe(0.75);
});
