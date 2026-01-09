<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
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
        ->toThrow(StringTypeException::class, 'String "5.0" has no valid strict integer value');

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
    expect(fn() => IntegerNonNegative::fromString('5.0'))->toThrow(StringTypeException::class);
});

it('fails creating NonNegativeInt from negative string', function (): void {
    expect(fn() => IntegerNonNegative::fromString('-1'))->toThrow(IntegerTypeException::class);
});

it('toString returns scalar string for NonNegativeInt', function (): void {
    expect((new IntegerNonNegative(0))->toString())->toBe('0');
});

it('fails creating NonNegativeInt from float string', function (): void {
    expect(fn() => IntegerNonNegative::fromString('5.5'))->toThrow(StringTypeException::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerNonNegative::tryFromString('1')->jsonSerialize())->toBeInt();
});
it('accepts non-negative integers and exposes value/toString', function (): void {
    $z = new IntegerNonNegative(0);
    $p = IntegerNonNegative::fromInt(10);

    expect($z->value())->toBe(0)
        ->and($z->toInt())->toBe(0)
        ->and($z->toString())->toBe('0')
        ->and((string) $z)->toBe('0')
        ->and($p->value())->toBe(10)
        ->and($p->toInt())->toBe(10)
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
            ->toThrow(StringTypeException::class);
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
    $fromTrue = IntegerNonNegative::tryFromMixed(true);
    $fromFalse = IntegerNonNegative::tryFromMixed(false);
    $badNeg = IntegerNonNegative::tryFromMixed(-1);
    $badFloatish = IntegerNonNegative::tryFromMixed('1.0');
    $badArr = IntegerNonNegative::tryFromMixed(['x']);
    $badNull = IntegerNonNegative::tryFromMixed(null);
    $badObj = IntegerNonNegative::tryFromMixed(new stdClass());

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
        ->and($fromTrue)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($fromTrue->value())->toBe(1)
        ->and($fromFalse)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($fromFalse->value())->toBe(0)
        ->and($okStringable)->toBeInstanceOf(IntegerNonNegative::class)
        ->and($okStringable->value())->toBe(7)
        ->and($badNeg)->toBeInstanceOf(Undefined::class)
        ->and($badFloatish)->toBeInstanceOf(Undefined::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class)
        ->and($badObj)->toBeInstanceOf(Undefined::class);
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

it('fromFloat creates instance from float with exact integer value', function (): void {
    $v = IntegerNonNegative::fromFloat(5.0);
    expect($v->value())->toBe(5);
});

it('toFloat converts to float and kills RemoveDoubleCast mutant', function (): void {
    $v = new IntegerNonNegative(42);
    $f = $v->toFloat();
    expect($f)->toBe(42.0)
        ->and($f)->toBeFloat();

    expect(\is_float($v->toFloat()))->toBeTrue();
});

it('toBool converts to bool', function (): void {
    $zero = new IntegerNonNegative(0);
    $positive = new IntegerNonNegative(5);
    expect($zero->toBool())->toBeFalse()
        ->and($positive->toBool())->toBeTrue();
});

it('fromBool creates instance from boolean value', function (): void {
    $fromTrue = IntegerNonNegative::fromBool(true);
    $fromFalse = IntegerNonNegative::fromBool(false);
    expect($fromTrue->value())->toBe(1)
        ->and($fromFalse->value())->toBe(0);
});

it('toFloat throws when precision would be lost', function (): void {
    $largeValue = new IntegerNonNegative(\PHP_INT_MAX);
    expect(fn() => $largeValue->toFloat())
        ->toThrow(IntegerTypeException::class, 'cannot be converted to float without losing precision');
});

it('round-trip conversion preserves value: int → string → int', function (): void {
    $original = 42;
    $v1 = IntegerNonNegative::fromInt($original);
    $str = $v1->toString();
    $v2 = IntegerNonNegative::fromString($str);

    expect($v2->value())->toBe($original);
});

it('round-trip conversion preserves value: string → int → string', function (): void {
    $original = '123';
    $v1 = IntegerNonNegative::fromString($original);
    $int = $v1->toInt();
    $v2 = IntegerNonNegative::fromInt($int);

    expect($v2->toString())->toBe($original);
});

it('multiple round-trips preserve value integrity', function (): void {
    $values = [0, 1, 42, 100, 999];

    foreach ($values as $original) {
        // int → string → int → string → int
        $result = IntegerNonNegative::fromString(
            IntegerNonNegative::fromInt(
                IntegerNonNegative::fromString(
                    IntegerNonNegative::fromInt($original)->toString()
                )->toInt()
            )->toString()
        )->value();

        expect($result)->toBe($original);
    }
});

it('isTypeOf returns true when class matches', function (): void {
    $v = IntegerNonNegative::fromInt(5);
    expect($v->isTypeOf(IntegerNonNegative::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = IntegerNonNegative::fromInt(5);
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = IntegerNonNegative::fromInt(5);
    expect($v->isTypeOf('NonExistentClass', IntegerNonNegative::class, 'AnotherClass'))->toBeTrue();
});
