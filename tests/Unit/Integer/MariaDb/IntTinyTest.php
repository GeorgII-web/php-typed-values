<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\MariaDb\IntTiny;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts values within signed tinyint range and preserves value', function (): void {
    $a = new IntTiny(-128);
    $b = IntTiny::fromInt(0);
    $c = IntTiny::fromInt(127);

    expect($a->value())->toBe(-128)
        ->and($a->toString())->toBe('-128')
        ->and($b->value())->toBe(0)
        ->and($b->toString())->toBe('0')
        ->and($c->value())->toBe(127)
        ->and($c->toString())->toBe('127');
});

it('fromString parses integers and enforces tinyint bounds', function (): void {
    $v = IntTiny::fromString('-5');
    expect($v->value())->toBe(-5)
        ->and($v->toString())->toBe('-5');
});

it('throws when value is below -128', function (): void {
    expect(fn() => new IntTiny(-129))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "-129"');
});

it('throws when value is above 127', function (): void {
    expect(fn() => IntTiny::fromInt(128))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "128"');
});

it('fromString throws on non-integer strings (strict check)', function (): void {
    expect(fn() => IntTiny::fromString('12.3'))
        ->toThrow(IntegerTypeException::class, 'String "12.3" has no valid strict integer value');
});

it('IntTiny::tryFromString returns value within -128..127', function (): void {
    $vMin = IntTiny::tryFromString('-128');
    $v0 = IntTiny::tryFromString('0');
    $vMax = IntTiny::tryFromString('127');

    expect($vMin)
        ->toBeInstanceOf(IntTiny::class)
        ->and($vMin->value())->toBe(-128)
        ->and($v0)
        ->toBeInstanceOf(IntTiny::class)
        ->and($v0->value())->toBe(0)
        ->and($vMax)
        ->toBeInstanceOf(IntTiny::class)
        ->and($vMax->value())->toBe(127);
});

it('IntTiny::tryFromString returns Undefined outside range and for non-integer strings', function (): void {
    expect(IntTiny::tryFromString('128'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntTiny::tryFromString('5.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntTiny::tryFromInt returns value within range and Undefined otherwise', function (): void {
    $ok = IntTiny::tryFromInt(-5);
    $bad = IntTiny::tryFromInt(200);

    expect($ok)
        ->toBeInstanceOf(IntTiny::class)
        ->and($ok->value())->toBe(-5)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});
