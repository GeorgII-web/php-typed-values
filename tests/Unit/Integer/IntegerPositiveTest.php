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

it('jsonSerialize returns integer', function (): void {
    expect(IntegerPositive::tryFromString('1')->jsonSerialize())->toBeInt();
});

it('accepts positive integers and exposes value/toString', function (): void {
    $v = new IntegerPositive(1);

    expect($v->value())
        ->toBe(1)
        ->and($v->toString())
        ->toBe('1')
        ->and((string) $v)
        ->toBe('1');
});

it('throws on zero or negative integers in constructor', function (): void {
    expect(fn() => new IntegerPositive(0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"')
        ->and(fn() => new IntegerPositive(-1))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "-1"');
});

it('fromInt and fromString construct only for > 0 and throw otherwise', function (): void {
    expect(IntegerPositive::fromInt(5)->value())->toBe(5)
        ->and(IntegerPositive::fromString('9')->value())->toBe(9);

    // Failing paths
    expect(fn() => IntegerPositive::fromInt(0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"')
        ->and(fn() => IntegerPositive::fromString('0'))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('fromString respects strict integer parsing rules', function (): void {
    // leading zeros, plus sign, decimals, or spaces are rejected by IntType::assertIntegerString
    foreach (['01', '+1', '1.0', ' 1', '1 ', 'a'] as $bad) {
        expect(fn() => IntegerPositive::fromString($bad))
            ->toThrow(IntegerTypeException::class, \sprintf('String "%s" has no valid strict integer value', $bad));
    }
});

it('tryFromInt/tryFromString return Undefined on invalid and instance on valid', function (): void {
    $okI = IntegerPositive::tryFromInt(2);
    $badI0 = IntegerPositive::tryFromInt(0);
    $badIn = IntegerPositive::tryFromInt(-10);

    $okS = IntegerPositive::tryFromString('3');
    $badS0 = IntegerPositive::tryFromString('0');
    $badSBad = IntegerPositive::tryFromString('01');

    expect($okI)->toBeInstanceOf(IntegerPositive::class)
        ->and($okI->value())->toBe(2)
        ->and($okS)->toBeInstanceOf(IntegerPositive::class)
        ->and($okS->value())->toBe(3)
        ->and($badI0)->toBeInstanceOf(Undefined::class)
        ->and($badIn)->toBeInstanceOf(Undefined::class)
        ->and($badS0)->toBeInstanceOf(Undefined::class)
        ->and($badSBad)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns native int', function (): void {
    expect(IntegerPositive::fromInt(7)->jsonSerialize())->toBeInt()->toBe(7);
});

it('tryFromMixed returns instance for integer-like inputs and Undefined otherwise', function (): void {
    $okInt = IntegerPositive::tryFromMixed(1);
    $okStr = IntegerPositive::tryFromMixed('9');
    $badZero = IntegerPositive::tryFromMixed(0);
    $badNeg = IntegerPositive::tryFromMixed(-1);
    $badFloatish = IntegerPositive::tryFromMixed('1.0');
    $badArr = IntegerPositive::tryFromMixed(['x']);
    $badNull = IntegerPositive::tryFromMixed(null);

    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return '7';
        }
    };
    $okStringable = IntegerPositive::tryFromMixed($stringable);

    expect($okInt)->toBeInstanceOf(IntegerPositive::class)
        ->and($okInt->value())->toBe(1)
        ->and($okStr)->toBeInstanceOf(IntegerPositive::class)
        ->and($okStr->value())->toBe(9)
        ->and($okStringable)->toBeInstanceOf(IntegerPositive::class)
        ->and($okStringable->value())->toBe(7)
        ->and($badZero)->toBeInstanceOf(Undefined::class)
        ->and($badNeg)->toBeInstanceOf(Undefined::class)
        ->and($badFloatish)->toBeInstanceOf(Undefined::class)
        ->and($badArr)->toBeInstanceOf(Undefined::class)
        ->and($badNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty returns false for IntegerPositive', function (): void {
    $a = new IntegerPositive(1);
    $b = IntegerPositive::fromInt(9);

    expect($a->isEmpty())->toBeFalse()
        ->and($b->isEmpty())->toBeFalse();
});
