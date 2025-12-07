<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerStandard::tryFromString returns value on valid integer string', function (): void {
    $v = IntegerStandard::tryFromString('123');

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())
        ->toBe(123);
});

it('IntegerStandard::tryFromString returns Undefined on invalid integer string', function (): void {
    $v = IntegerStandard::tryFromString('5.0');

    expect($v)->toBeInstanceOf(Undefined::class);
});

it('IntegerStandard::tryFromInt always returns value for any int', function (): void {
    $v = IntegerStandard::tryFromInt(-999);

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())
        ->toBe(-999);
});

it('IntegerStandard::fromInt returns instance and preserves value', function (): void {
    $v = IntegerStandard::fromInt(42);

    expect($v)
        ->toBeInstanceOf(IntegerStandard::class)
        ->and($v->value())->toBe(42)
        ->and($v->toString())->toBe('42');
});

it('IntegerStandard::fromString throws on non-integer strings (strict check)', function (): void {
    expect(fn() => IntegerStandard::fromString('12.3'))
        ->toThrow(IntegerTypeException::class, 'String "12.3" has no valid strict integer value');
});

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
