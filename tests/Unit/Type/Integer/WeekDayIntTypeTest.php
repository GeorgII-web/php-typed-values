<?php

declare(strict_types=1);

use PhpTypedValues\Type\Integer\WeekDayInt;

it('creates WeekDayInt from int 1', function (): void {
    expect(WeekDayInt::fromInt(1)->value())->toBe(1);
});

it('creates WeekDayInt from int 7', function (): void {
    expect(WeekDayInt::fromInt(7)->value())->toBe(7);
});

it('fails on 8', function (): void {
    expect(fn() => WeekDayInt::fromInt(8))->toThrow(InvalidArgumentException::class);
});

it('fails on 0', function (): void {
    expect(fn() => WeekDayInt::fromInt(0))->toThrow(InvalidArgumentException::class);
});
