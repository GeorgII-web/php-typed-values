<?php

declare(strict_types=1);

use GeorgiiWeb\PhpTypedValues\Exception\IntegerTypeException;
use GeorgiiWeb\PhpTypedValues\Types\Integer\Integer;

it('creates Integer from int', function (): void {
    expect(Integer::fromInt(5)->value())->toBe(5);
});

it('creates Integer from string', function (): void {
    expect(Integer::fromString('5')->value())->toBe(5);
});

it('creates Integer from string value and fails', function (): void {
    expect(fn() => Integer::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('creates Integer from invalid string and fail', function (): void {
    expect(function () {
        try {
            // invalid integer string (contains decimal point)
            Integer::fromString('34.0');
        } catch (Throwable $e) {
            throw new IntegerTypeException('Failed to create Integer from string', previous: $e);
        }
    })->toThrow(IntegerTypeException::class);
});
