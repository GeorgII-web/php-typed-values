<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Type\Integer\PositiveInt;

it('creates PositiveInt', function (): void {
    expect(PositiveInt::fromInt(1)->value())->toBe(1);
});

it('fails on 0', function (): void {
    expect(fn() => PositiveInt::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('fails on negatives', function (): void {
    expect(fn() => PositiveInt::fromInt(-1))->toThrow(IntegerTypeException::class);
});
