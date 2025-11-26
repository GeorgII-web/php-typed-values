<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerPositive;

it('creates IntegerPositive', function (): void {
    expect(IntegerPositive::fromInt(1)->value())->toBe(1);
});

it('fails on 0', function (): void {
    expect(fn() => IntegerPositive::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('fails on negatives', function (): void {
    expect(fn() => IntegerPositive::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates IntegerPositive from string', function (): void {
    expect(IntegerPositive::fromString('1')->value())->toBe(1);
});

it('fails IntegerPositive from integerish string', function (): void {
    expect(fn() => IntegerPositive::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating IntegerPositive from string 0', function (): void {
    expect(fn() => IntegerPositive::fromString('0'))->toThrow(IntegerTypeException::class);
});

it('fails creating IntegerPositive from negative string', function (): void {
    expect(fn() => IntegerPositive::fromString('-3'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for IntegerPositive', function (): void {
    expect((new IntegerPositive(3))->toString())->toBe('3');
});

it('fails creating IntegerPositive from float string', function (): void {
    expect(fn() => IntegerPositive::fromString('5.5'))->toThrow(IntegerTypeException::class);
});
