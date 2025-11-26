<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandard;

it('creates Integer from int', function (): void {
    expect(IntegerStandard::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(IntegerStandard::fromString('5')->value())->toBe(5);
});

it('fails on "integer-ish" float string', function (): void {
    expect(fn() => IntegerStandard::fromString('5.'))->toThrow(IntegerTypeException::class);
});

it('fails on float string', function (): void {
    expect(fn() => IntegerStandard::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('fails on type mismatch', function (): void {
    expect(function () {
        try {
            // invalid integer string (contains decimal point)
            IntegerStandard::fromInt('34.66');
        } catch (Throwable $e) {
            throw new IntegerTypeException('Failed to create Integer from string', previous: $e);
        }
    })->toThrow(IntegerTypeException::class);
});
