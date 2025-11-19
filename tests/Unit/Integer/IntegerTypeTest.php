<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Integer\IntegerBasic;

it('creates Integer from int', function (): void {
    expect(IntegerBasic::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(IntegerBasic::fromString('5')->value())->toBe(5);
});

it('fails on "integer-ish" float string', function (): void {
    expect(IntegerBasic::fromString('5.0')->value())->toBe(5);
});

it('fails on float string', function (): void {
    expect(fn() => IntegerBasic::fromString('5.5'))->toThrow(NumericTypeException::class);
});

it('fails on type mismatch', function (): void {
    expect(function () {
        try {
            // invalid integer string (contains decimal point)
            IntegerBasic::fromInt('34.66');
        } catch (Throwable $e) {
            throw new NumericTypeException('Failed to create Integer from string', previous: $e);
        }
    })->toThrow(NumericTypeException::class);
});
