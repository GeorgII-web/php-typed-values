<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\MariaDb\IntTiny;

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
