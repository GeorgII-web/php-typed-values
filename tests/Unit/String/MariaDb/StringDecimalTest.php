<?php

declare(strict_types=1);

use PhpTypedValues\Exception\DecimalStringTypeException;
use PhpTypedValues\String\MariaDb\StringDecimal;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts valid decimal strings and preserves value/toString', function (): void {
    $a = new StringDecimal('0');
    $b = StringDecimal::fromString('123');
    $c = StringDecimal::fromString('-5');
    $d = StringDecimal::fromString('3.14');

    expect($a->value())->toBe('0')
        ->and($a->toString())->toBe('0')
        ->and($b->value())->toBe('123')
        ->and($c->value())->toBe('-5')
        ->and($d->toString())->toBe('3.14');
});

it('throws on malformed decimal strings', function (): void {
    expect(fn() => new StringDecimal(''))
        ->toThrow(DecimalStringTypeException::class, 'Expected decimal string')
        ->and(fn() => StringDecimal::fromString('abc'))
        ->toThrow(DecimalStringTypeException::class, 'Expected decimal string')
        ->and(fn() => StringDecimal::fromString('.5'))
        ->toThrow(DecimalStringTypeException::class, 'Expected decimal string')
        ->and(fn() => StringDecimal::fromString('1.'))
        ->toThrow(DecimalStringTypeException::class, 'Expected decimal string')
        ->and(fn() => StringDecimal::fromString('+1'))
        ->toThrow(DecimalStringTypeException::class, 'Expected decimal string');
});

it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
    $ok = StringDecimal::tryFromString('42.5');
    $bad = StringDecimal::tryFromString('nope');

    expect($ok)
        ->toBeInstanceOf(StringDecimal::class)
        ->and($ok->value())
        ->toBe('42.5')
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
    expect(StringDecimal::fromString('1')->toFloat())->toBe(1.0)
        ->and(StringDecimal::fromString('-2')->toFloat())->toBe(-2.0)
        ->and(StringDecimal::fromString('1.5')->toFloat())->toBe(1.5);

    expect(fn() => StringDecimal::fromString('1.50')->toFloat())
        ->toThrow(DecimalStringTypeException::class, 'Unexpected float conversion')
        ->and(fn() => StringDecimal::fromString('0.0')->toFloat())
        ->toThrow(DecimalStringTypeException::class, 'Unexpected float conversion')
        ->and(fn() => StringDecimal::fromString('2.000')->toFloat())
        ->toThrow(DecimalStringTypeException::class, 'Unexpected float conversion');
});

it('jsonSerialize returns string', function (): void {
    expect(StringDecimal::fromString('1.1')->jsonSerialize())->toBeString();
});
