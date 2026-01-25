<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\DecimalStringTypeException;
use PhpTypedValues\String\MariaDb\StringDecimal;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringDecimal', function () {
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
            ->toThrow(DecimalStringTypeException::class, 'Expected non-empty decimal string')
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

    it('__toString returns the original decimal string', function (): void {
        $d = new StringDecimal('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs (as strings)
        $fromString = StringDecimal::tryFromMixed('42');
        $fromStringFloat = StringDecimal::tryFromMixed('3.1415');

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '-5.5';
            }
        };
        $fromStringable = StringDecimal::tryFromMixed($stringable);

        // invalid inputs
        $fromArray = StringDecimal::tryFromMixed(['x']);
        $fromNull = StringDecimal::tryFromMixed(null);
        $fromInt = StringDecimal::tryFromMixed(123);
        $fromObject = StringDecimal::tryFromMixed(new stdClass());

        expect($fromString)->toBeInstanceOf(StringDecimal::class)
            ->and($fromString->value())->toBe('42')
            ->and($fromStringFloat)->toBeInstanceOf(StringDecimal::class)
            ->and($fromStringFloat->value())->toBe('3.1415')
            ->and($fromStringable)->toBeInstanceOf(StringDecimal::class)
            ->and($fromStringable->value())->toBe('-5.5')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromInt)->toBeInstanceOf(StringDecimal::class)
            ->and($fromInt->value())->toBe('123')
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = StringDecimal::fromString('10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = StringDecimal::tryFromString('abc');
        $u2 = StringDecimal::tryFromMixed(['x']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($ok->isUndefined())->not()->toBeTrue()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringDecimal::fromString('10.5');
        expect($v->isTypeOf(StringDecimal::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringDecimal::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringDecimal::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass', StringDecimal::class, 'AnotherClass'))->toBeTrue();
    });

    it('isEmpty is always false for StringDecimal', function (): void {
        $d = new StringDecimal('0');
        expect($d->isEmpty())->toBeFalse()
            ->and($d->isEmpty())->not()->toBeTrue();
    });

    it('covers conversions for StringDecimal', function (): void {
        expect(fn() => StringDecimal::fromBool(true))->toThrow(DecimalStringTypeException::class)
            ->and(fn() => StringDecimal::fromBool(false))->toThrow(DecimalStringTypeException::class)
            ->and(StringDecimal::fromInt(123)->value())->toBe('123')
            ->and(StringDecimal::fromFloat(1.2)->value())->toBe('1.19999999999999996');

        expect(fn() => StringDecimal::fromString('true'))->toThrow(DecimalStringTypeException::class);

        $vInt = StringDecimal::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and(fn() => $vInt->toBool())->toThrow(IntegerTypeException::class);

        $vFloat = StringDecimal::fromString('1.2');
        expect($vFloat->toFloat())->toBe(1.2);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return StringDecimal for valid inputs', function (): void {
        expect(StringDecimal::tryFromFloat(1.2))->toBeInstanceOf(StringDecimal::class)
            ->and(StringDecimal::tryFromInt(123))->toBeInstanceOf(StringDecimal::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringDecimalTest extends StringDecimal
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('test');
    }

    public static function fromString(string $value): static
    {
        throw new Exception('test');
    }
}

it('StringDecimal::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
    expect(StringDecimalTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
        ->and(StringDecimalTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
        ->and(StringDecimalTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
        ->and(StringDecimalTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
        ->and(StringDecimalTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
});
