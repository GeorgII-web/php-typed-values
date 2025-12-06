<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatPositive;

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
