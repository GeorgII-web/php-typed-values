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
