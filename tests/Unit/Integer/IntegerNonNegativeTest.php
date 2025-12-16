<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

it('IntegerNonNegative::tryFromString returns value for >= 0', function (): void {
    $v0 = IntegerNonNegative::tryFromString('0');
    $v5 = IntegerNonNegative::tryFromString('5');

    expect($v0)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($v0->value())
        ->toBe(0)
        ->and($v5)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($v5->value())
        ->toBe(5);
});

it('IntegerNonNegative::tryFromString returns Undefined for negatives and non-integer strings', function (): void {
    expect(IntegerNonNegative::tryFromString('-1'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerNonNegative::tryFromString('5.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerNonNegative::tryFromInt returns value for >= 0 and Undefined for negatives', function (): void {
    $ok = IntegerNonNegative::tryFromInt(0);
    $bad = IntegerNonNegative::tryFromInt(-10);

    expect($ok)
        ->toBeInstanceOf(IntegerNonNegative::class)
        ->and($ok->value())
        ->toBe(0)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('IntegerNonNegative throws on negative values in ctor and fromInt', function (): void {
    expect(fn() => new IntegerNonNegative(-1))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-1"')
        ->and(fn() => IntegerNonNegative::fromInt(-10))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-10"');
});

it('IntegerNonNegative::fromString enforces strict integer and non-negativity', function (): void {
    // Strict integer check
    expect(fn() => IntegerNonNegative::fromString('5.0'))
        ->toThrow(IntegerTypeException::class, 'String "5.0" has no valid strict integer value');

    // Non-negativity check after casting
    expect(fn() => IntegerNonNegative::fromString('-1'))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-1"');

    // Success path
    $v = IntegerNonNegative::fromString('0');
    expect($v->value())->toBe(0);
});

it('creates NonNegativeInt', function (): void {
    expect((new IntegerNonNegative(0))->value())->toBe(0);
});

it('fails on negatives', function (): void {
    expect(fn() => IntegerNonNegative::fromInt(-1))->toThrow(IntegerTypeException::class);
});

it('creates NonNegativeInt from string 0', function (): void {
    expect(IntegerNonNegative::fromString('0')->value())->toBe(0);
});

it('fails NonNegativeInt from integerish string', function (): void {
    expect(fn() => IntegerNonNegative::fromString('5.0'))->toThrow(IntegerTypeException::class);
});

it('fails creating NonNegativeInt from negative string', function (): void {
    expect(fn() => IntegerNonNegative::fromString('-1'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for NonNegativeInt', function (): void {
    expect((new IntegerNonNegative(0))->toString())->toBe('0');
});

it('fails creating NonNegativeInt from float string', function (): void {
    expect(fn() => IntegerNonNegative::fromString('5.5'))->toThrow(IntegerTypeException::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerNonNegative::tryFromString('1')->jsonSerialize())->toBeInt();
});
it('accepts non-negative integers and exposes value/toString', function (): void {
    $z = new IntegerNonNegative(0);
    $p = IntegerNonNegative::fromInt(10);

    expect($z->value())->toBe(0)
        ->and($z->toString())->toBe('0')
        ->and((string) $z)->toBe('0')
        ->and($p->value())->toBe(10)
        ->and($p->toString())->toBe('10');
});

it('throws on negative values in constructor/fromInt', function (): void {
    expect(fn() => new IntegerNonNegative(-1))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-1"')
        ->and(fn() => IntegerNonNegative::fromInt(-5))
        ->toThrow(IntegerTypeException::class, 'Expected non-negative integer, got "-5"');
});

it('fromString uses strict integer parsing and accepts only canonical numbers', function (): void {
    expect(IntegerNonNegative::fromString('0')->value())->toBe(0)
        ->and(IntegerNonNegative::fromString('42')->value())->toBe(42);

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerNonNegative::fromString($bad))
            ->toThrow(IntegerTypeException::class, \sprintf('String "%s" has no valid strict integer value', $bad));
    }
});

it('tryFromInt/tryFromString return Undefined for invalid inputs', function (): void {
    $okI = IntegerNonNegative::tryFromInt(0);
    $badI = IntegerNonNegative::tryFromInt(-1);
    $okS = IntegerNonNegative::tryFromString('5');
    $badS1 = IntegerNonNegative::tryFromString('-1');
    $badS2 = IntegerNonNegative::tryFromString('01');

    expect($okI)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($okS)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($badI)->toBeInstanceOf(Undefined::class)
        ->and($badS1)->toBeInstanceOf(Undefined::class)
        ->and($badS2)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerNonNegative::fromInt(11)->jsonSerialize())->toBe(11);
});

it('tryFromMixed returns instance for integer-like inputs and Undefined otherwise', function (): void {
    $okInt = IntegerNonNegative::tryFromMixed(0);
    $okStr = IntegerNonNegative::tryFromMixed('12');
    $badNeg = IntegerNonNegative::tryFromMixed(-1);
    $badFloatish = IntegerNonNegative::tryFromMixed('1.0');
    $badArr = IntegerNonNegative::tryFromMixed(['x']);
    $badNull = IntegerNonNegative::tryFromMixed(null);

    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '7';
        }
    };
    $okStringable = IntegerNonNegative::tryFromMixed($stringable);

    expect($okInt)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($okInt->value())->toBe(0)
        ->and($okStr)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($okStr->value())->toBe(12)
        ->and($okStringable)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($okStringable->value())->toBe(7)
        ->and($badNeg)->toBeInstanceOf(Undefined::class)
        ->and($badFloatish)->toBeInstanceOf(Undefined::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for IntegerNonNegative', function (): void {
    $a = new IntegerNonNegative(0);
    $b = IntegerNonNegative::fromInt(5);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined is always false', function (): void {
    expect(IntegerNonNegative::fromInt(0)->isUndefined())->toBeFalse()
        ->and(IntegerNonNegative::fromInt(1)->isUndefined())->toBeFalse();
});
