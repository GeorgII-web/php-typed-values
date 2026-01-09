<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\Alias\NonNegative;

it('NonNegativeInt alias factories return NonNegativeInt instance', function (): void {
    $a = NonNegative::fromInt(0);
    $b = NonNegative::fromString('12');

    expect($a)->toBeInstanceOf(NonNegative::class)
        ->and($a::class)->toBe(NonNegative::class)
        ->and($a->value())->toBe(0)
        ->and($b)->toBeInstanceOf(NonNegative::class)
        ->and($b->value())->toBe(12);
});

it('creates NonNegativeInt', function (): void {
    expect((new NonNegative(0))->value())->toBe(0);
});

it('fails on negatives', function (): void {
    expect(fn() => NonNegative::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates NonNegativeInt from string 0', function (): void {
    expect(NonNegative::fromString('0')->value())->toBe(0);
});

it('fails NonNegativeInt from integerish string', function (): void {
    expect(fn() => NonNegative::fromString('5.0'))->toThrow(StringTypeException::class);
});

it('fails creating NonNegativeInt from negative string', function (): void {
    expect(fn() => NonNegative::fromString('-1'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for NonNegativeInt', function (): void {
    expect((new NonNegative(0))->toString())->toBe('0');
});

it('fails creating NonNegativeInt from float string', function (): void {
    expect(fn() => NonNegative::fromString('5.5'))->toThrow(StringTypeException::class);
});
