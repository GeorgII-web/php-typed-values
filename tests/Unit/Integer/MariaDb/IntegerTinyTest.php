<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerTiny;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts values within signed tinyint range and preserves value', function (): void {
    $a = new IntegerTiny(-128);
    $b = IntegerTiny::fromInt(0);
    $c = IntegerTiny::fromInt(127);

    expect($a->value())->toBe(-128)
        ->and($a->toString())->toBe('-128')
        ->and($b->value())->toBe(0)
        ->and($b->toString())->toBe('0')
        ->and($c->value())->toBe(127)
        ->and($c->toString())->toBe('127');
});

it('fromString parses integers and enforces tinyint bounds', function (): void {
    $v = IntegerTiny::fromString('-5');
    expect($v->value())->toBe(-5)
        ->and($v->toString())->toBe('-5');
});

it('throws when value is below -128', function (): void {
    expect(fn() => new IntegerTiny(-129))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "-129"');
});

it('throws when value is above 127', function (): void {
    expect(fn() => IntegerTiny::fromInt(128))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "128"');
});

it('fromString throws on non-integer strings (strict check)', function (): void {
    expect(fn() => IntegerTiny::fromString('12.3'))
        ->toThrow(IntegerTypeException::class, 'String "12.3" has no valid strict integer value');
});

it('IntTiny::tryFromString returns value within -128..127', function (): void {
    $vMin = IntegerTiny::tryFromString('-128');
    $v0 = IntegerTiny::tryFromString('0');
    $vMax = IntegerTiny::tryFromString('127');

    expect($vMin)
        ->toBeInstanceOf(IntegerTiny::class)
        ->and($vMin->value())->toBe(-128)
        ->and($v0)
        ->toBeInstanceOf(IntegerTiny::class)
        ->and($v0->value())->toBe(0)
        ->and($vMax)
        ->toBeInstanceOf(IntegerTiny::class)
        ->and($vMax->value())->toBe(127);
});

it('IntTiny::tryFromString returns Undefined outside range and for non-integer strings', function (): void {
    expect(IntegerTiny::tryFromString('128'))
        ->toBeInstanceOf(Undefined::class)
        ->and(IntegerTiny::tryFromString('5.0'))
        ->toBeInstanceOf(Undefined::class);
});

it('IntTiny::tryFromInt returns value within range and Undefined otherwise', function (): void {
    $ok = IntegerTiny::tryFromInt(-5);
    $bad = IntegerTiny::tryFromInt(200);

    expect($ok)
        ->toBeInstanceOf(IntegerTiny::class)
        ->and($ok->value())->toBe(-5)
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns integer', function (): void {
    expect(IntegerTiny::tryFromString('1')->jsonSerialize())->toBeInt();
});

it('accepts -128..127 and exposes value/toString', function (): void {
    $min = new IntegerTiny(-128);
    $max = IntegerTiny::fromInt(127);

    expect($min->value())->toBe(-128)
        ->and($min->toInt())->toBe(-128)
        ->and($min->toString())->toBe('-128')
        ->and((string) $min)->toBe('-128')
        ->and($max->value())->toBe(127)
        ->and($max->toInt())->toBe(127)
        ->and($max->toString())->toBe('127');
});

it('throws on values out of -128..127 in constructor/fromInt', function (): void {
    expect(fn() => new IntegerTiny(-129))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "-129"')
        ->and(fn() => IntegerTiny::fromInt(128))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "128"');
});

it('fromString enforces strict integer parsing and range', function (): void {
    expect(IntegerTiny::fromString('-5')->value())->toBe(-5)
        ->and(IntegerTiny::fromString('0')->toString())->toBe('0')
        ->and(IntegerTiny::fromString('127')->value())->toBe(127);

    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerTiny::fromString($bad))
            ->toThrow(IntegerTypeException::class);
    }

    // In-range strict parse ok; out-of-range strict parse -> domain error
    expect(fn() => IntegerTiny::fromString('128'))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "128"')
        ->and(fn() => IntegerTiny::fromString('-129'))
        ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127, got "-129"');
});

it('tryFromInt/tryFromString return Undefined on invalid and instance on valid', function (): void {
    $okI = IntegerTiny::tryFromInt(-1);
    $badI = IntegerTiny::tryFromInt(1000);
    $okS = IntegerTiny::tryFromString('5');
    $badS = IntegerTiny::tryFromString('01');

    expect($okI)->toBeInstanceOf(IntegerTiny::class)
        ->and($okI->value())->toBe(-1)
        ->and($okS)->toBeInstanceOf(IntegerTiny::class)
        ->and($okS->value())->toBe(5)
        ->and($badI)->toBeInstanceOf(Undefined::class)
        ->and($badS)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerTiny::fromInt(-7)->jsonSerialize())->toBe(-7);
});

it('tryFromMixed returns instance for integer-like inputs within range and Undefined otherwise', function (): void {
    $okInt = IntegerTiny::tryFromMixed(-1);
    $okStr = IntegerTiny::tryFromMixed('127');
    $fromTrue = IntegerTiny::tryFromMixed(true);
    $fromFalse = IntegerTiny::tryFromMixed(false);
    $badLow = IntegerTiny::tryFromMixed(-129);
    $badHigh = IntegerTiny::tryFromMixed(128);
    $badFloatish = IntegerTiny::tryFromMixed('1.0');
    $badArr = IntegerTiny::tryFromMixed(['x']);
    $badNull = IntegerTiny::tryFromMixed(null);
    $badObj = IntegerTiny::tryFromMixed(new stdClass());

    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '0';
        }
    };
    $okStringable = IntegerTiny::tryFromMixed($stringable);

    expect($okInt)->toBeInstanceOf(IntegerTiny::class)
        ->and($okInt->value())->toBe(-1)
        ->and($okStr)->toBeInstanceOf(IntegerTiny::class)
        ->and($okStr->value())->toBe(127)
        ->and($fromTrue)->toBeInstanceOf(IntegerTiny::class)
        ->and($fromTrue->value())->toBe(1)
        ->and($fromFalse)->toBeInstanceOf(IntegerTiny::class)
        ->and($fromFalse->value())->toBe(0)
        ->and($okStringable)->toBeInstanceOf(IntegerTiny::class)
        ->and($okStringable->value())->toBe(0)
        ->and($badLow)->toBeInstanceOf(Undefined::class)
        ->and($badHigh)->toBeInstanceOf(Undefined::class)
        ->and($badFloatish)->toBeInstanceOf(Undefined::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class)
        ->and($badObj)->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for IntegerTiny', function (): void {
    $a = new IntegerTiny(-1);
    $b = IntegerTiny::fromInt(127);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});

it('isUndefined is always false', function (): void {
    expect(IntegerTiny::fromInt(0)->isUndefined())->toBeFalse()
        ->and(IntegerTiny::fromInt(1)->isUndefined())->toBeFalse();
});

it('fromFloat creates instance from float with exact integer value', function (): void {
    $v = IntegerTiny::fromFloat(5.0);
    expect($v->value())->toBe(5);
});

it('toFloat converts to float', function (): void {
    $v = new IntegerTiny(42);
    expect($v->toFloat())->toBe(42.0)
        ->and($v->toFloat())->toBeFloat();
});

it('toBool converts to bool', function (): void {
    $zero = new IntegerTiny(0);
    $positive = new IntegerTiny(5);
    expect($zero->toBool())->toBeFalse()
        ->and($positive->toBool())->toBeTrue();
});
