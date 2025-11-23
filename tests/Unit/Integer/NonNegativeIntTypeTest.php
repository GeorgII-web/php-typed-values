<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Integer\NonNegativeInt;

it('creates NonNegativeInt', function (): void {
    expect((new NonNegativeInt(0))->value())->toBe(0);
});

it('fails on negatives', function (): void {
    expect(fn() => NonNegativeInt::fromInt(-1))->toThrow(NumericTypeException::class);
});

it('creates NonNegativeInt from string 0', function (): void {
    expect(NonNegativeInt::fromString('0')->value())->toBe(0);
});

it('fails NonNegativeInt from integerish string', function (): void {
    expect(fn() => NonNegativeInt::fromString('5.0'))->toThrow(NumericTypeException::class);
});

it('fails creating NonNegativeInt from negative string', function (): void {
    expect(fn() => NonNegativeInt::fromString('-1'))->toThrow(NumericTypeException::class);
});

it('toString returns scalar string for NonNegativeInt', function (): void {
    expect((new NonNegativeInt(0))->toString())->toBe('0');
});

it('fails creating NonNegativeInt from float string', function (): void {
    expect(fn() => NonNegativeInt::fromString('5.5'))->toThrow(NumericTypeException::class);
});
