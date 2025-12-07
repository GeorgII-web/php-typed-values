<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerNonNegative::tryFromString returns value for >= 0', function (): void {
    $v0 = IntegerNonNegative::tryFromString('0');
    $v5 = IntegerNonNegative::tryFromString('5');

    expect($v0)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($v0->value())
        ->toBe(0)
        ->and($v5)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($v5->value())
        ->toBe(5);
});

it('IntegerNonNegative::tryFromString returns Undefined for negatives and non-integer strings', function (): void {
    expect(IntegerNonNegative::tryFromString('-1'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerNonNegative::tryFromString('5.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerNonNegative::tryFromInt returns value for >= 0 and Undefined for negatives', function (): void {
    $ok = IntegerNonNegative::tryFromInt(0);
    $bad = IntegerNonNegative::tryFromInt(-10);

    expect($ok)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($ok->value())
        ->toBe(0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerNonNegative throws on negative values in ctor and fromInt', function (): void {
    expect(fn() => new IntegerNonNegative(-1))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-1"')
        ->and(fn() => IntegerNonNegative::fromInt(-10))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-10"');
});

it('IntegerNonNegative::fromString enforces strict integer and non-negativity', function (): void {
    // Strict integer check
    expect(fn() => IntegerNonNegative::fromString('5.0'))
        ->toThrow(IntegerTypeException::class, 'String "5.0" has no valid strict integer value');

    // Non-negativity check after casting
    expect(fn() => IntegerNonNegative::fromString('-1'))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-1"');

    // Success path
    $v = IntegerNonNegative::fromString('0');
    expect($v->value())->toBe(0);
});
