<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandart;

it('creates Integer from int', function (): void {
    expect(IntegerStandart::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(IntegerStandart::fromString('5')->value())->toBe(5);
});

it('fails on "integer-ish" float string', function (): void {
    expect(fn() => IntegerStandart::fromString('5.'))->toThrow(IntegerTypeException::class);
});

it('fails on float string', function (): void {
    expect(fn() => IntegerStandart::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('fails on type mismatch', function (): void {
    expect(function () {
        try {
            // invalid integer string (contains decimal point)
            IntegerStandart::fromInt('34.66');
        } catch (Throwable $e) {
            throw new IntegerTypeException('Failed to create Integer from string', previous: $e);
        }
    })->toThrow(IntegerTypeException::class);
});
