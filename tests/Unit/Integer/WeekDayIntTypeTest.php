<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\NumericTypeException;
use PhpTypedValues\Integer\WeekDayInt;

it('creates WeekDayInt from int 1', function (): void {
    expect(WeekDayInt::fromInt(1)->value())->toBe(1);
});

it('creates WeekDayInt from int 7', function (): void {
    expect(WeekDayInt::fromInt(7)->value())->toBe(7);
});

it('fails on 8', function (): void {
    expect(fn() => WeekDayInt::fromInt(8))->toThrow(NumericTypeException::class);
});

it('fails on 0', function (): void {
    expect(fn() => WeekDayInt::fromInt(0))->toThrow(NumericTypeException::class);
});

it('creates WeekDayInt from string within range', function (): void {
    expect(WeekDayInt::fromString('1')->value())->toBe(1);
    expect(WeekDayInt::fromString('7')->value())->toBe(7);
});

it('creates WeekDayInt from integerish string', function (): void {
    expect(WeekDayInt::fromString('5.0')->value())->toBe(5);
});

it('fails creating WeekDayInt from out-of-range strings', function (): void {
    expect(fn() => WeekDayInt::fromString('0'))->toThrow(NumericTypeException::class);
    expect(fn() => WeekDayInt::fromString('8'))->toThrow(NumericTypeException::class);
});

it('toString returns scalar string for WeekDayInt', function (): void {
    expect((new WeekDayInt(3))->toString())->toBe('3');
});

it('fails creating WeekDayInt from float string', function (): void {
    expect(fn() => WeekDayInt::fromString('5.5'))->toThrow(NumericTypeException::class);
});
