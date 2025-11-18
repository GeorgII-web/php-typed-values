<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Type\Integer\Integer;

it('creates Integer from int', function (): void {
    expect(Integer::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(Integer::fromString('5')->value())->toBe(5);
});

it('fails on "integer-ish" float string', function (): void {
    expect(fn() => Integer::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails on float string', function (): void {
    expect(fn() => Integer::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('fails on type mismatch', function (): void {
    expect(function () {
        try {
            // invalid integer string (contains decimal point)
            Integer::fromInt('34.66');
        } catch (Throwable $e) {
            throw new IntegerTypeException('Failed to create Integer from string', previous: $e);
        }
    })->toThrow(IntegerTypeException::class);
});
