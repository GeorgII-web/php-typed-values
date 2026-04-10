<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Decimal;

use Exception;
use PhpTypedValues\Decimal\DecimalNonPositive;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\NonPositiveDecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(DecimalNonPositive::class);

describe('DecimalNonPositive', function () {
    it('accepts valid non-positive decimal strings and preserves value/toString', function (): void {
        $a = new DecimalNonPositive('0.0');
        $b = DecimalNonPositive::fromString('-123.0');
        $c = DecimalNonPositive::fromString('-3.14');

        expect($a->value())->toBe('0.0')
            ->and($a->toString())->toBe('0.0')
            ->and($b->value())->toBe('-123.0')
            ->and($c->toString())->toBe('-3.14');
    });

    it('throws on positive or malformed decimal strings', function (): void {
        expect(fn() => new DecimalNonPositive(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalNonPositive::fromString(' '))
            ->toThrow(DecimalTypeException::class, 'String " " has no valid decimal value')
            ->and(fn() => DecimalNonPositive::fromString('abc'))
            ->toThrow(DecimalTypeException::class, 'String "abc" has no valid strict decimal value')
            ->and(fn() => DecimalNonPositive::fromString('.5'))
            ->toThrow(DecimalTypeException::class, 'String ".5" has no valid strict decimal value')
            ->and(fn() => DecimalNonPositive::fromString('1.'))
            ->toThrow(DecimalTypeException::class, 'String "1." has no valid strict decimal value')
            ->and(fn() => DecimalNonPositive::fromString('+1'))
            ->toThrow(DecimalTypeException::class, 'String "+1" has no valid strict decimal value')
            ->and(fn() => DecimalNonPositive::fromString('0.1'))
            ->toThrow(NonPositiveDecimalTypeException::class, 'Decimal "0.1" is not a non-positive value')
            ->and(fn() => DecimalNonPositive::fromString('1.0'))
            ->toThrow(NonPositiveDecimalTypeException::class, 'Decimal "1.0" is not a non-positive value');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalNonPositive::tryFromString('-42.5');
        $bad = DecimalNonPositive::tryFromString('nope');
        $pos = DecimalNonPositive::tryFromString('1.0');

        expect($ok)
            ->toBeInstanceOf(DecimalNonPositive::class)
            ->and($ok->value())
            ->toBe('-42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($pos)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalNonPositive::fromString('0.0')->toFloat())->toBe(0.0)
            ->and(DecimalNonPositive::fromString('-1.5')->toFloat())->toBe(-1.5);
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalNonPositive::fromString('-1.1')->jsonSerialize())->toBeString();
    });

    it('fromNull throws NonPositiveDecimalTypeException', function (): void {
        expect(fn() => DecimalNonPositive::fromNull(null))->toThrow(NonPositiveDecimalTypeException::class, 'Value cannot be null');
    });

    it('toNull throws NonPositiveDecimalTypeException', function (): void {
        expect(fn() => (new DecimalNonPositive('-1.0'))->toNull())->toThrow(NonPositiveDecimalTypeException::class, 'Value cannot be null');
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalNonPositive('-3.14');
        expect((string) $d)->toBe('-3.14')
            ->and($d->__toString())->toBe('-3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs
        expect(DecimalNonPositive::tryFromMixed('-42'))->toBeInstanceOf(DecimalNonPositive::class)
            ->and(DecimalNonPositive::tryFromMixed(-3.14))->toBeInstanceOf(DecimalNonPositive::class)
            ->and(DecimalNonPositive::tryFromMixed(-100))->toBeInstanceOf(DecimalNonPositive::class)
            ->and(DecimalNonPositive::tryFromMixed(false))->toBeInstanceOf(DecimalNonPositive::class);

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '-5.5';
            }
        };
        expect(DecimalNonPositive::tryFromMixed($stringable))->toBeInstanceOf(DecimalNonPositive::class);

        // invalid inputs
        expect(DecimalNonPositive::tryFromMixed(['x']))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositive::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositive::tryFromMixed(123))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositive::tryFromMixed(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositive::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = DecimalNonPositive::fromString('-10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = DecimalNonPositive::tryFromString('abc');
        $u2 = DecimalNonPositive::tryFromMixed(['x']);
        $u3 = DecimalNonPositive::tryFromInt(1);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue()
            ->and($u3->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalNonPositive::fromString('-10.5');
        expect($v->isTypeOf(DecimalNonPositive::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalNonPositive::fromString('-10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = DecimalNonPositive::fromString('-10.5');
        expect($v->isTypeOf('NonExistentClass', DecimalNonPositive::class, 'AnotherClass'))->toBeTrue();
    });

    it('isEmpty is always false for DecimalNonPositive', function (): void {
        $d = new DecimalNonPositive('0.0');
        expect($d->isEmpty())->toBeFalse();
    });

    it('covers conversions for DecimalNonPositive', function (): void {
        expect(DecimalNonPositive::fromBool(false)->value())->toBe('0.0')
            ->and(DecimalNonPositive::fromInt(-123)->value())->toBe('-123.0')
            ->and(DecimalNonPositive::fromFloat(-1.2)->value())->toBe('-1.19999999999999996')
            ->and(DecimalNonPositive::fromDecimal('-1.23')->value())->toBe('-1.23');

        $vInt = DecimalNonPositive::fromString('-123');
        expect($vInt->toInt())->toBe(-123)
            ->and($vInt->toDecimal())->toBe('-123')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalNonPositive for valid inputs', function (): void {
        expect(DecimalNonPositive::tryFromFloat(-1.2))->toBeInstanceOf(DecimalNonPositive::class)
            ->and(DecimalNonPositive::tryFromInt(-123))->toBeInstanceOf(DecimalNonPositive::class)
            ->and(DecimalNonPositive::tryFromBool(false))->toBeInstanceOf(DecimalNonPositive::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalNonPositiveTest extends DecimalNonPositive
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

describe('Throwing static for DecimalNonPositive', function () {
    it('DecimalNonPositive::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalNonPositiveTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositiveTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositiveTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositiveTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositiveTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonPositiveTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
