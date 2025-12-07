<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerWeekDay;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerWeekDay::tryFromString returns value for 1..7', function (): void {
    $v1 = IntegerWeekDay::tryFromString('1');
    $v7 = IntegerWeekDay::tryFromString('7');

    expect($v1)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($v1->value())
        ->toBe(1)
        ->and($v7)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($v7->value())
        ->toBe(7);
});

it('IntegerWeekDay::tryFromString returns Undefined outside 1..7 and for non-integer strings', function (): void {
    expect(IntegerWeekDay::tryFromString('0'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerWeekDay::tryFromString('8'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerWeekDay::tryFromString('3.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerWeekDay::tryFromInt returns value for 1..7 and Undefined otherwise', function (): void {
    $ok = IntegerWeekDay::tryFromInt(3);
    $bad = IntegerWeekDay::tryFromInt(0);

    expect($ok)
        ->toBeInstanceOf(IntegerWeekDay::class)
        ->and($ok->value())
        ->toBe(3)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerWeekDay throws on values outside 1..7 in ctor and fromInt', function (): void {
    expect(fn() => new IntegerWeekDay(0))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromInt(8))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('IntegerWeekDay::fromString enforces strict integer and range 1..7', function (): void {
    // Strict integer check
    expect(fn() => IntegerWeekDay::fromString('3.0'))
        ->toThrow(IntegerTypeException::class, 'String "3.0" has no valid strict integer value');

    // Range checks after casting
    expect(fn() => IntegerWeekDay::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromString('8'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');

    // Success path
    $v = IntegerWeekDay::fromString('6');
    expect($v->value())->toBe(6);
});
