<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\TypeException;
use PhpTypedValues\Integer\PositiveInt;

it('creates PositiveInt', function (): void {
    expect(PositiveInt::fromInt(1)->value())->toBe(1);
});

it('fails on 0', function (): void {
    expect(fn() => PositiveInt::fromInt(0))->toThrow(TypeException::class);
});

it('fails on negatives', function (): void {
    expect(fn() => PositiveInt::fromInt(-1))->toThrow(TypeException::class);
});

it('creates PositiveInt from string', function (): void {
    expect(PositiveInt::fromString('1')->value())->toBe(1);
});

it('creates PositiveInt from integerish string', function (): void {
    expect(PositiveInt::fromString('5.0')->value())->toBe(5);
});

it('fails creating PositiveInt from string 0', function (): void {
    expect(fn() => PositiveInt::fromString('0'))->toThrow(TypeException::class);
});

it('fails creating PositiveInt from negative string', function (): void {
    expect(fn() => PositiveInt::fromString('-3'))->toThrow(TypeException::class);
});

it('toString returns scalar string for PositiveInt', function (): void {
    expect((new PositiveInt(3))->toString())->toBe('3');
});

it('fails creating PositiveInt from float string', function (): void {
    expect(fn() => PositiveInt::fromString('5.5'))->toThrow(TypeException::class);
});
