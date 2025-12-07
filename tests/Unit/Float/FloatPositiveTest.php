<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

it('constructs positive float via constructor', function (): void {
    $v = new FloatPositive(0.1);
    expect($v->value())->toBe(0.1)
        ->and($v->toString())->toBe('0.1');
});

it('creates from float factory', function (): void {
    $v = FloatPositive::fromFloat(1.5);
    expect($v->value())->toBe(1.5);
});

it('creates from string factory', function (): void {
    $v = FloatPositive::fromString('2.5');
    expect($v->value())->toBe(2.5)
        ->and($v->toString())->toBe('2.5');
});

it('throws on zero via constructor', function (): void {
    expect(fn() => new FloatPositive(0.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"');
});

it('throws on zero via fromString', function (): void {
    expect(fn() => FloatPositive::fromString('0.0'))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"');
});

it('throws on negative via constructor', function (): void {
    expect(fn() => new FloatPositive(-0.1))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-0.1"');
});

it('throws on negative via fromString', function (): void {
    expect(fn() => FloatPositive::fromString('-1.23'))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-1.23"');
});

it('throws on string not float', function (): void {
    expect(fn() => FloatPositive::fromString('unknown'))
        ->toThrow(FloatTypeException::class, 'String "unknown" has no valid float value');
});

it('FloatPositive::tryFromString returns value for > 0.0 and Undefined otherwise', function (): void {
    $ok = FloatPositive::tryFromString('0.1');
    $badZero = FloatPositive::tryFromString('0');
    $badNeg = FloatPositive::tryFromString('-0.1');
    $badStr = FloatPositive::tryFromString('abc');

    expect($ok)
        ->toBeInstanceOf(FloatPositive::class)
        ->and($ok->value())->toBe(0.1)
        ->and($badZero)->toBeInstanceOf(Undefined::class)
        ->and($badNeg)->toBeInstanceOf(Undefined::class)
        ->and($badStr)->toBeInstanceOf(Undefined::class);
});

it('FloatPositive::tryFromFloat returns value for positive int and Undefined otherwise', function (): void {
    $ok = FloatPositive::tryFromFloat(2);
    $bad = FloatPositive::tryFromFloat(0);

    expect($ok)
        ->toBeInstanceOf(FloatPositive::class)
        ->and($ok->value())
        ->toBe(2.0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('FloatPositive throws on non-positive values in ctor and fromFloat', function (): void {
    expect(fn() => new FloatPositive(0.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"')
        ->and(fn() => FloatPositive::fromFloat(-1.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-1"');
});

it('FloatPositive::fromString enforces numeric and positivity', function (): void {
    // Non-numeric
    expect(fn() => FloatPositive::fromString('abc'))
        ->toThrow(FloatTypeException::class, 'String "abc" has no valid float value');

    // Positivity
    expect(fn() => FloatPositive::fromString('0'))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"');

    // Success path
    $v = FloatPositive::fromString('1.25');
    expect($v->value())->toBe(1.25);
});
