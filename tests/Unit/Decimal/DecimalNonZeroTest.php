<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Decimal;

use Exception;
use PhpTypedValues\Decimal\DecimalNonZero;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\NonZeroDecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(DecimalNonZero::class);

describe('DecimalNonZero', function () {
    it('accepts valid non-zero decimal strings and preserves value/toString', function (): void {
        $a = new DecimalNonZero('0.1');
        $b = DecimalNonZero::fromString('-123.0');
        $c = DecimalNonZero::fromString('3.14');

        expect($a->value())->toBe('0.1')
            ->and($a->toString())->toBe('0.1')
            ->and($b->value())->toBe('-123.0')
            ->and($c->toString())->toBe('3.14');
    });

    it('throws on zero or malformed decimal strings', function (): void {
        expect(fn() => new DecimalNonZero(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalNonZero::fromString(' '))
            ->toThrow(DecimalTypeException::class, 'String " " has no valid decimal value')
            ->and(fn() => DecimalNonZero::fromString('abc'))
            ->toThrow(DecimalTypeException::class, 'String "abc" has no valid strict decimal value')
            ->and(fn() => DecimalNonZero::fromString('0.0'))
            ->toThrow(NonZeroDecimalTypeException::class, 'Decimal "0.0" is not a non-zero value')
            ->and(fn() => DecimalNonZero::fromString('-0.0'))
            ->toThrow(NonZeroDecimalTypeException::class, 'Decimal "-0.0" is not a non-zero value');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalNonZero::tryFromString('42.5');
        $bad = DecimalNonZero::tryFromString('nope');
        $zero = DecimalNonZero::tryFromString('0.0');

        expect($ok)
            ->toBeInstanceOf(DecimalNonZero::class)
            ->and($ok->value())
            ->toBe('42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($zero)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalNonZero::fromString('1.0')->toFloat())->toBe(1.0)
            ->and(DecimalNonZero::fromString('-1.5')->toFloat())->toBe(-1.5);
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalNonZero::fromString('1.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalNonZero('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs
        expect(DecimalNonZero::tryFromMixed('42'))->toBeInstanceOf(DecimalNonZero::class)
            ->and(DecimalNonZero::tryFromMixed(3.14))->toBeInstanceOf(DecimalNonZero::class)
            ->and(DecimalNonZero::tryFromMixed(123))->toBeInstanceOf(DecimalNonZero::class)
            ->and(DecimalNonZero::tryFromMixed(true))->toBeInstanceOf(DecimalNonZero::class);

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '5.5';
            }
        };
        expect(DecimalNonZero::tryFromMixed($stringable))->toBeInstanceOf(DecimalNonZero::class);

        // invalid inputs
        expect(DecimalNonZero::tryFromMixed(['x']))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZero::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZero::tryFromMixed(0))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZero::tryFromMixed(0.0))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZero::tryFromMixed(false))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZero::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = DecimalNonZero::fromString('10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = DecimalNonZero::tryFromString('abc');
        $u2 = DecimalNonZero::tryFromMixed(['x']);
        $u3 = DecimalNonZero::tryFromInt(0);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue()
            ->and($u3->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalNonZero::fromString('10.5');
        expect($v->isTypeOf(DecimalNonZero::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalNonZero::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for DecimalNonZero', function (): void {
        $d = new DecimalNonZero('1.0');
        expect($d->isEmpty())->toBeFalse();
    });

    it('covers conversions for DecimalNonZero', function (): void {
        expect(DecimalNonZero::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalNonZero::fromInt(123)->value())->toBe('123.0')
            ->and(DecimalNonZero::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(DecimalNonZero::fromDecimal('1.23')->value())->toBe('1.23');

        $vInt = DecimalNonZero::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and($vInt->toDecimal())->toBe('123')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalNonZero for valid inputs', function (): void {
        expect(DecimalNonZero::tryFromFloat(1.2))->toBeInstanceOf(DecimalNonZero::class)
            ->and(DecimalNonZero::tryFromInt(123))->toBeInstanceOf(DecimalNonZero::class)
            ->and(DecimalNonZero::tryFromBool(true))->toBeInstanceOf(DecimalNonZero::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalNonZeroTest extends DecimalNonZero
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromDecimal(string $value): static
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

describe('Throwing static for DecimalNonZero', function () {
    it('DecimalNonZero::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalNonZeroTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZeroTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZeroTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZeroTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZeroTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonZeroTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
