<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\Alias\NonNegative;

it('creates NonNegativeInt', function (): void {
    expect((new NonNegative(0))->value())->toBe(0);
});

it('fails on negatives', function (): void {
    expect(fn() => NonNegative::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates NonNegativeInt from string 0', function (): void {
    expect(NonNegative::fromString('0')->value())->toBe(0);
});

it('fails NonNegativeInt from integerish string', function (): void {
    expect(fn() => NonNegative::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating NonNegativeInt from negative string', function (): void {
    expect(fn() => NonNegative::fromString('-1'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for NonNegativeInt', function (): void {
    expect((new NonNegative(0))->toString())->toBe('0');
});

it('fails creating NonNegativeInt from float string', function (): void {
    expect(fn() => NonNegative::fromString('5.5'))->toThrow(IntegerTypeException::class);
});
