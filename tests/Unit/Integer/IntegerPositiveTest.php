<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerPositive::tryFromString returns value for > 0', function (): void {
    $v = IntegerPositive::tryFromString('5');

    expect($v)
        ->toBeInstanceOf(IntegerPositive::class)
        ->and($v->value())
        ->toBe(5);
});

it('IntegerPositive::tryFromString returns Undefined for 0 and non-integer strings', function (): void {
    expect(IntegerPositive::tryFromString('0'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerPositive::tryFromString('5.0'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerPositive::tryFromString('-3'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerPositive::tryFromInt returns value for positive int and Undefined otherwise', function (): void {
    $ok = IntegerPositive::tryFromInt(10);
    $bad = IntegerPositive::tryFromInt(-1);

    expect($ok)
        ->toBeInstanceOf(IntegerPositive::class)
        ->and($ok->value())
        ->toBe(10)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerPositive throws on non-positive values in ctor and fromInt', function (): void {
    expect(fn() => new IntegerPositive(0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"')
        ->and(fn() => IntegerPositive::fromInt(-1))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "-1"');
});

it('IntegerPositive::fromString enforces strict integer and positivity', function (): void {
    // Strict integer check
    expect(fn() => IntegerPositive::fromString('5.0'))
        ->toThrow(IntegerTypeException::class, 'String "5.0" has no valid strict integer value');

    // Positivity check after casting
    expect(fn() => IntegerPositive::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');

    // Success path
    $v = IntegerPositive::fromString('7');
    expect($v->value())->toBe(7);
});

it('creates IntegerPositive', function (): void {
    expect(IntegerPositive::fromInt(1)->value())->toBe(1);
});

it('fails on 0', function (): void {
    expect(fn() => IntegerPositive::fromInt(0))->toThrow(IntegerTypeException::class);
});

it('fails on negatives', function (): void {
    expect(fn() => IntegerPositive::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates IntegerPositive from string', function (): void {
    expect(IntegerPositive::fromString('1')->value())->toBe(1);
});

it('fails IntegerPositive from integerish string', function (): void {
    expect(fn() => IntegerPositive::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating IntegerPositive from string 0', function (): void {
    expect(fn() => IntegerPositive::fromString('0'))->toThrow(IntegerTypeException::class);
});

it('fails creating IntegerPositive from negative string', function (): void {
    expect(fn() => IntegerPositive::fromString('-3'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for IntegerPositive', function (): void {
    expect((new IntegerPositive(3))->toString())->toBe('3');
});

it('fails creating IntegerPositive from float string', function (): void {
    expect(fn() => IntegerPositive::fromString('5.5'))->toThrow(IntegerTypeException::class);
});
