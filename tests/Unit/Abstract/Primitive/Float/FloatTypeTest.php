<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatStandard;

it('fromString parses valid float strings including negatives, decimals, and scientific', function (): void {
    expect(FloatStandard::fromString('-15.25')->value())->toBe(-15.25)
        ->and(FloatStandard::fromString('5')->value())->toBe(5.0)
        ->and(FloatStandard::fromString('5.0')->value())->toBe(5.0)
        ->and(FloatStandard::fromString('0.0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0.0')->value())->toBe(0.0)
        ->and(FloatStandard::fromString('-0.0')->toString())->toBe('-0')
        ->and(FloatStandard::fromString('1.2345678912345')->toString())->toBe('1.2345678912345')
        ->and(FloatStandard::fromString('42')->value())->toBe(42.0)
        ->and(FloatStandard::fromString('42')->toString())->toBe('42');
});

it('fromString rejects non-numeric strings and magic conversions', function (): void {
    expect(fn() => FloatStandard::fromString('5a'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('a5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString(''))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('abc'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('--5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5,5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5 5'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('1.23456789012345678'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0.666666666666666629'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('5.00000'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005.0'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('0005.000'))->toThrow(FloatTypeException::class)
        ->and(fn() => FloatStandard::fromString('1e3'))->toThrow(FloatTypeException::class);
});

it('fromString precious for string and float difference', function (): void {
    expect(FloatStandard::fromFloat(2 / 3)->value())->toBe(0.6666666666666666) // accepts "messy" real float value
    ->and(FloatStandard::fromString((string) (2 / 3))->value())->toBe(0.66666666666667); // "string cast" uses serialize_precision to have a precious value
});

it('__toString proxies to toString for FloatType', function (): void {
    $v = new FloatStandard(1.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('1.5');
});

it('fromFloat returns exact value and toString matches', function (): void {
    $f1 = FloatStandard::fromFloat(-10.5);
    expect($f1->value())->toBe(-10.5)
        ->and($f1->toString())->toBe('-10.5');

    $f2 = FloatStandard::fromFloat(0.0);
    expect($f2->value())->toBe(0.0)
        ->and($f2->toString())->toBe('0');
});
