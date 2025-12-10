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

it('creates WeekDayInt from int 1', function (): void {
    expect(IntegerWeekDay::fromInt(1)->value())->toBe(1);
});

it('creates WeekDayInt from int 7', function (): void {
    expect(IntegerWeekDay::fromInt(7)->value())->toBe(7);
});

it('fails on 8', function (): void {
    expect(fn() => IntegerWeekDay::fromInt(8))->toThrow(IntegerTypeException::class);
});

it('fails on 0', function (): void {
    expect(fn() => IntegerWeekDay::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('creates WeekDayInt from string within range', function (): void {
    expect(IntegerWeekDay::fromString('1')->value())->toBe(1);
    expect(IntegerWeekDay::fromString('7')->value())->toBe(7);
});

it('creates WeekDayInt from integerish string', function (): void {
    expect(fn() => IntegerWeekDay::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating WeekDayInt from out-of-range strings', function (): void {
    expect(fn() => IntegerWeekDay::fromString('0'))->toThrow(IntegerTypeException::class);
    expect(fn() => IntegerWeekDay::fromString('8'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for WeekDayInt', function (): void {
    expect((new IntegerWeekDay(3))->toString())->toBe('3');
});

it('fails creating WeekDayInt from float string', function (): void {
    expect(fn() => IntegerWeekDay::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerWeekDay::tryFromString('1')->jsonSerialize())->toBeInt();
});
it('accepts 1..7 and exposes value/toString', function (): void {
    $one = new IntegerWeekDay(1);
    $seven = IntegerWeekDay::fromInt(7);

    expect($one->value())->toBe(1)
        ->and($one->toString())->toBe('1')
        ->and((string) $one)->toBe('1')
        ->and($seven->value())->toBe(7)
        ->and($seven->toString())->toBe('7');
});

it('throws on values out of 1..7 in constructor/fromInt', function (): void {
    expect(fn() => new IntegerWeekDay(0))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromInt(8))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('fromString enforces strict integer parsing and range', function (): void {
    expect(IntegerWeekDay::fromString('2')->value())->toBe(2)
        ->and(IntegerWeekDay::fromString('6')->toString())->toBe('6');

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerWeekDay::fromString($bad))
            ->toThrow(IntegerTypeException::class, \sprintf('String "%s" has no valid strict integer value', $bad));
    }

    // Strict string passes validation but out of range -> domain error
    expect(fn() => IntegerWeekDay::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"')
        ->and(fn() => IntegerWeekDay::fromString('8'))
        ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "8"');
});

it('tryFromInt/tryFromString return Undefined on invalid and instance on valid', function (): void {
    $okI = IntegerWeekDay::tryFromInt(3);
    $badI = IntegerWeekDay::tryFromInt(9);
    $okS = IntegerWeekDay::tryFromString('4');
    $badS = IntegerWeekDay::tryFromString('01');

    expect($okI)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okI->value())->toBe(3)
        ->and($okS)->toBeInstanceOf(IntegerWeekDay::class)
        ->and($okS->value())->toBe(4)
        ->and($badI)->toBeInstanceOf(Undefined::class)
        ->and($badS)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerWeekDay::fromInt(5)->jsonSerialize())->toBe(5);
});
