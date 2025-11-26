<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerWeekDay;

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
