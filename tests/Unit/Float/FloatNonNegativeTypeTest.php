<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatNonNegative;

it('accepts non-negative floats via fromFloat and toString matches', function (): void {
    $f0 = FloatNonNegative::fromFloat(0.0);
    expect($f0->value())->toBe(0.0)
        ->and($f0->toString())->toBe('0');

    $f1 = FloatNonNegative::fromFloat(1.5);
    expect($f1->value())->toBe(1.5)
        ->and($f1->toString())->toBe('1.5');
});

it('parses non-negative numeric strings via fromString', function (): void {
    expect(FloatNonNegative::fromString('0')->value())->toBe(0.0)
        ->and(FloatNonNegative::fromString('0.0')->value())->toBe(0.0)
        ->and(FloatNonNegative::fromString('3.14')->value())->toBe(3.14)
        ->and(FloatNonNegative::fromString('1e2')->value())->toBe(100.0)
        ->and(FloatNonNegative::fromString('42')->toString())->toBe('42');
});

it('rejects negative values', function (): void {
    expect(fn() => new FloatNonNegative(-0.001))
        ->toThrow(FloatTypeException::class);
    expect(fn() => FloatNonNegative::fromFloat(-0.001))
        ->toThrow(FloatTypeException::class);
});

it('rejects non-numeric or negative strings', function (): void {
    // Non-numeric
    foreach (['', 'abc', '5,5'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(FloatTypeException::class);
    }

    // Numeric but negative
    foreach (['-1', '-0.1'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(FloatTypeException::class);
    }
});
