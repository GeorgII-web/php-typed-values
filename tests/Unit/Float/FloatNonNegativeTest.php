<?php

declare(strict_types=1);

use PhpTypedValues\Exception\FloatTypeException;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts non-negative floats via fromFloat and toString matches', function (): void {
    $f0 = FloatNonNegative::fromFloat(0.0);
    expect($f0->value())->toBe(0.0)
        ->and($f0->toString())->toBe('0');

    $f1 = FloatNonNegative::fromFloat(1.5);
    expect($f1->value())->toBe(1.5)
        ->and($f1->toString())->toBe('1.5');
});

it('parses non-negative numeric strings via fromString', function (): void {
    expect(FloatNonNegative::fromString('0')->value())->toBe(0.0)
        ->and(FloatNonNegative::fromString('0.0')->value())->toBe(0.0)
        ->and(FloatNonNegative::fromString('3.14')->value())->toBe(3.14)
        ->and(FloatNonNegative::fromString('1e2')->value())->toBe(100.0)
        ->and(FloatNonNegative::fromString('42')->toString())->toBe('42');
});

it('rejects negative values', function (): void {
    expect(fn() => new FloatNonNegative(-0.001))
        ->toThrow(FloatTypeException::class);
    expect(fn() => FloatNonNegative::fromFloat(-0.001))
        ->toThrow(FloatTypeException::class);
});

it('rejects non-numeric or negative strings', function (): void {
    // Non-numeric
    foreach (['', 'abc', '5,5'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(FloatTypeException::class);
    }

    // Numeric but negative
    foreach (['-1', '-0.1'] as $str) {
        expect(fn() => FloatNonNegative::fromString($str))
            ->toThrow(FloatTypeException::class);
    }
});

it('FloatNonNegative::tryFromString returns value for >= 0.0 and Undefined otherwise', function (): void {
    $ok0 = FloatNonNegative::tryFromString('0');
    $ok = FloatNonNegative::tryFromString('0.5');
    $bad = FloatNonNegative::tryFromString('-0.1');
    $badStr = FloatNonNegative::tryFromString('abc');

    expect($ok0)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok0->value())->toBe(0.0)
        ->and($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())->toBe(0.5)
        ->and($bad)->toBeInstanceOf(Undefined::class)
        ->and($badStr)->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative::tryFromFloat returns value for >= 0 and Undefined otherwise', function (): void {
    $ok = FloatNonNegative::tryFromFloat(0);
    $bad = FloatNonNegative::tryFromFloat(-1);

    expect($ok)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($ok->value())
        ->toBe(0.0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('FloatNonNegative throws on negative values in ctor and fromFloat', function (): void {
    expect(fn() => new FloatNonNegative(-0.1))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.1"')
        ->and(fn() => FloatNonNegative::fromFloat(-1.0))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-1"');
});

it('FloatNonNegative::fromString enforces numeric and non-negativity', function (): void {
    // Non-numeric
    expect(fn() => FloatNonNegative::fromString('abc'))
        ->toThrow(FloatTypeException::class, 'String "abc" has no valid float value');

    // Non-negativity
    expect(fn() => FloatNonNegative::fromString('-0.5'))
        ->toThrow(FloatTypeException::class, 'Expected non-negative float, got "-0.5"');

    // Success path
    $v = FloatNonNegative::fromString('0.75');
    expect($v->value())->toBe(0.75);
});

it('jsonSerialize returns float', function (): void {
    expect(FloatNonNegative::tryFromString('1.1')->jsonSerialize())->toBeFloat();
});

it('__toString casts same as toString and equals string representation', function (): void {
    $v = FloatNonNegative::fromFloat(2.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('2.5');
});

it('accepts negative zero and normalizes to "-0" in toString', function (): void {
    // fromFloat with -0.0 must be treated as non-negative
    $v = FloatNonNegative::fromFloat(-0.0);

    expect($v->value())
        ->toBe(0.0)
        ->and($v->toString())
        ->toBe('-0');
});

it('tryFromMixed accepts numeric strings/ints/floats and returns Undefined for invalid', function (): void {
    $s = FloatNonNegative::tryFromMixed('3.5');
    $i = FloatNonNegative::tryFromMixed(2);
    $f = FloatNonNegative::tryFromMixed(4.25);
    $bad1 = FloatNonNegative::tryFromMixed('-1');
    $bad2 = FloatNonNegative::tryFromMixed('abc');
    $bad3 = FloatNonNegative::tryFromMixed(['x']);
    $bad4 = FloatNonNegative::tryFromMixed(new stdClass());

    expect($s)
        ->toBeInstanceOf(FloatNonNegative::class)
        ->and($s->value())->toBe(3.5)
        ->and($i)->toBeInstanceOf(FloatNonNegative::class)
        ->and($i->value())->toBe(2.0)
        ->and($f)->toBeInstanceOf(FloatNonNegative::class)
        ->and($f->value())->toBe(4.25)
        ->and($bad1)->toBeInstanceOf(Undefined::class)
        ->and($bad2)->toBeInstanceOf(Undefined::class)
        ->and($bad3)->toBeInstanceOf(Undefined::class)
        ->and($bad4)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize equals value() for valid instances', function (): void {
    $v = FloatNonNegative::fromString('10.5');
    expect($v->jsonSerialize())->toBe($v->value());
});

it('isEmpty returns false for FloatNonNegative', function (): void {
    $a = new FloatNonNegative(0.0);
    $b = FloatNonNegative::fromFloat(3.14);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});
