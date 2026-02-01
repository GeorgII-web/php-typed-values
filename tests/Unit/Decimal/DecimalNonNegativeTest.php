<?php

declare(strict_types=1);

namespace Decimal;

use const PHP_INT_MAX;

use Exception;
use PhpTypedValues\Decimal\DecimalNonNegative;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

use function sprintf;

describe('DecimalNonNegative', function () {
    it('accepts valid non-negative decimal strings and preserves value/toString', function (): void {
        $a = new DecimalNonNegative('0.1');
        $b = DecimalNonNegative::fromString('123.0');
        $c = DecimalNonNegative::fromString('3.14');
        $d = DecimalNonNegative::fromString('0.0');

        expect($a->value())->toBe('0.1')
            ->and($a->toString())->toBe('0.1')
            ->and($b->value())->toBe('123.0')
            ->and($c->toString())->toBe('3.14')
            ->and($d->value())->toBe('0.0');
    });

    it('throws on negative or malformed decimal strings', function (): void {
        expect(fn() => new DecimalNonNegative(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalNonNegative::fromString(' '))
            ->toThrow(DecimalTypeException::class, 'String " " has no valid decimal value')
            ->and(fn() => DecimalNonNegative::fromString('abc'))
            ->toThrow(DecimalTypeException::class, 'String "abc" has no valid strict decimal value')
            ->and(fn() => DecimalNonNegative::fromString('.5'))
            ->toThrow(DecimalTypeException::class, 'String ".5" has no valid strict decimal value')
            ->and(fn() => DecimalNonNegative::fromString('1.'))
            ->toThrow(DecimalTypeException::class, 'String "1." has no valid strict decimal value')
            ->and(fn() => DecimalNonNegative::fromString('+1'))
            ->toThrow(DecimalTypeException::class, 'String "+1" has no valid strict decimal value')
            ->and(fn() => DecimalNonNegative::fromString('-1.0'))
            ->toThrow(DecimalTypeException::class, 'Decimal "-1.0" is not a non-negative value');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalNonNegative::tryFromString('42.5');
        $bad = DecimalNonNegative::tryFromString('nope');
        $zero = DecimalNonNegative::tryFromString('0.0');

        expect($ok)
            ->toBeInstanceOf(DecimalNonNegative::class)
            ->and($ok->value())
            ->toBe('42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($zero)
            ->toBeInstanceOf(DecimalNonNegative::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalNonNegative::fromString('1.0')->toFloat())->toBe(1.0)
            ->and(DecimalNonNegative::fromString('1.5')->toFloat())->toBe(1.5)
            ->and(DecimalNonNegative::fromString('0.0')->toFloat())->toBe(0.0);

        expect(fn() => DecimalNonNegative::fromString('1.50')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "1.50" has no valid strict decimal value')
            ->and(fn() => DecimalNonNegative::fromString('2.000')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "2.000" has no valid strict decimal value');
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalNonNegative::fromString('1.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalNonNegative('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs (as strings)
        $fromString = DecimalNonNegative::tryFromMixed('42');
        $fromStringFloat = DecimalNonNegative::tryFromMixed('3.1415');
        $fromZero = DecimalNonNegative::tryFromMixed('0.0');

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '5.5';
            }
        };
        $fromStringable = DecimalNonNegative::tryFromMixed($stringable);

        // invalid inputs
        $fromArray = DecimalNonNegative::tryFromMixed(['x']);
        $fromNull = DecimalNonNegative::tryFromMixed(null);
        $fromInt = DecimalNonNegative::tryFromMixed(-123);
        $fromObject = DecimalNonNegative::tryFromMixed(new stdClass());

        expect($fromString)->toBeInstanceOf(DecimalNonNegative::class)
            ->and($fromString->value())->toBe('42')
            ->and($fromStringFloat)->toBeInstanceOf(DecimalNonNegative::class)
            ->and($fromStringFloat->value())->toBe('3.1415')
            ->and($fromZero)->toBeInstanceOf(DecimalNonNegative::class)
            ->and($fromZero->value())->toBe('0.0')
            ->and($fromStringable)->toBeInstanceOf(DecimalNonNegative::class)
            ->and($fromStringable->value())->toBe('5.5')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromInt)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = DecimalNonNegative::fromString('10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = DecimalNonNegative::tryFromString('abc');
        $u2 = DecimalNonNegative::tryFromMixed(['x']);
        $u3 = DecimalNonNegative::tryFromInt(-1);

        expect($ok->isUndefined())->toBeFalse()
            ->and($ok->isUndefined())->not()->toBeTrue()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue()
            ->and($u3->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalNonNegative::fromString('10.5');
        expect($v->isTypeOf(DecimalNonNegative::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalNonNegative::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = DecimalNonNegative::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass', DecimalNonNegative::class, 'AnotherClass'))->toBeTrue();
    });

    it('isEmpty is always false for DecimalNonNegative', function (): void {
        $d = new DecimalNonNegative('1.0');
        expect($d->isEmpty())->toBeFalse()
            ->and($d->isEmpty())->not()->toBeTrue();
    });

    it('covers conversions for DecimalNonNegative', function (): void {
        expect(DecimalNonNegative::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalNonNegative::fromInt(123)->value())->toBe('123.0')
            ->and(DecimalNonNegative::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(DecimalNonNegative::fromDecimal('1.23')->value())->toBe('1.23');

        expect(fn() => DecimalNonNegative::fromString('true'))->toThrow(DecimalTypeException::class);

        $vInt = DecimalNonNegative::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and($vInt->toDecimal())->toBe('123')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);

        expect(fn() => DecimalNonNegative::fromString('1.2')->toFloat())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalNonNegative for valid inputs', function (): void {
        expect(DecimalNonNegative::tryFromFloat(1.2))->toBeInstanceOf(DecimalNonNegative::class)
            ->and(DecimalNonNegative::tryFromInt(123))->toBeInstanceOf(DecimalNonNegative::class)
            ->and(DecimalNonNegative::tryFromBool(true))->toBeInstanceOf(DecimalNonNegative::class)
            ->and(DecimalNonNegative::tryFromBool(false))->toBeInstanceOf(DecimalNonNegative::class);
    });

    it('cast float > decimal', function (): void {
        expect(DecimalNonNegative::fromFloat(1.5)->toString())->toBe('1.5')
            ->and(DecimalNonNegative::fromFloat(0.1)->toString())->toBe('0.10000000000000001')
            ->and(DecimalNonNegative::fromFloat(1)->toString())->toBe('1.0')
            ->and(DecimalNonNegative::fromFloat(0.0)->toString())->toBe('0.0');

        expect(fn() => DecimalNonNegative::fromFloat(-1.0))->toThrow(DecimalTypeException::class);
    });

    it('cast decimal > decimal', function (): void {
        expect(DecimalNonNegative::fromDecimal('1.0')->toString())->toBe('1.0')
            ->and(DecimalNonNegative::fromDecimal('0.0')->toString())->toBe('0.0');

        expect(fn() => DecimalNonNegative::fromDecimal('-1.0'))->toThrow(DecimalTypeException::class);
    });

    it('cast bool > decimal', function (): void {
        expect(DecimalNonNegative::fromBool(true)->toString())->toBe('1.0')
            ->and(DecimalNonNegative::fromBool(false)->toString())->toBe('0.0');
    });

    it('cast int > decimal', function (): void {
        expect(DecimalNonNegative::fromInt(123)->toString())->toBe('123.0')
            ->and(DecimalNonNegative::fromInt(99999999999999999)->toString())->toBe('99999999999999999.0')
            ->and(DecimalNonNegative::fromInt(PHP_INT_MAX)->toString())->toBe(PHP_INT_MAX . '.0')
            ->and(DecimalNonNegative::fromInt(1)->toString())->toBe('1.0')
            ->and(DecimalNonNegative::fromInt(0)->toString())->toBe('0.0');

        expect(fn() => DecimalNonNegative::fromInt(-1))->toThrow(DecimalTypeException::class);
    });

    it('can be used with bcmath functions', function (): void {
        $a = DecimalNonNegative::fromString('1.23');
        $b = DecimalNonNegative::fromString('2.34');

        $sum = bcadd($a->value(), $b->value(), 2);
        $diff = bcsub($b->value(), $a->value(), 2);
        $mul = bcmul($a->value(), $b->value(), 4);
        $div = bcdiv($b->value(), $a->value(), 10);

        expect($sum)->toBe('3.57')
            ->and($diff)->toBe('1.11')
            ->and($mul)->toBe('2.8782')
            ->and($div)->toBe('1.9024390243');
    });

    it('get round-trips decimal strings without changes', function (string $label, string $value): void {
        $decimal = DecimalNonNegative::fromString($value);

        expect($decimal->toString())
            ->toBe($value, sprintf('Case "%s" failed for value "%s"', $label, $value));
    })->with([
        ['simple_decimal', '123.0'],
        ['int_with_dot_zero', '1.0'],
        ['simple_fraction', '1.5'],
        ['pi_like', '3.14'],
        ['leading_zero_fraction', '10.01'],
        ['big_int', '999999999999999999'],
        ['big_decimal', '12345678901234567890.123456789'],
        ['biger_decimal', '1234567890123456789023532452345342532452345.12345678923453245324534253245342534253'],
        ['zero_decimal', '0.0'],
    ]);

    it('rejects invalid decimal strings', function (string $label, string $value): void {
        try {
            DecimalNonNegative::fromString($value);
            $this->fail(sprintf('Case "%s" did not throw for value "%s"', $label, $value));
        } catch (TypeException) {
            expect(true)->toBeTrue();
        }
    })->with([
        ['empty_string', ''],
        ['space', ' '],
        ['tab', "\t"],
        ['dot_only', '.'],
        ['zero', '0'],
        ['negative_decimal', '-1.0'],
        ['leading_dot', '.5'],
        ['integer', '1'],
        ['minus_integer', '-1'],
        ['trailing_dot', '1.'],
        ['plus_sign', '+1'],
        ['leading_zero_with_fraction', '01.230'],
        ['double_zero', '00'],
        ['leading_zeros', '0001'],
        ['trailing_zeros_fractional', '1.2300'],
        ['double_dot', '1..0'],
        ['multiple_dots', '1.2.3'],
        ['scientific_notation_lower', '1e2'],
        ['scientific_notation_upper', '1E2'],
        ['comma_decimal', '1,23'],
        ['numeric_separators', '1_000'],
        ['invalid_sign_placement', '1-1'],
        ['non_numeric', 'abc'],
        ['hex', '0x10'],
    ]);

    it('validates very large numbers compatible with bcmath', function (): void {
        $large = '123456789012345678901234567890.123456789';
        $decimal = DecimalNonNegative::fromString($large);

        expect($decimal->value())->toBe($large);

        $added = bcadd($decimal->value(), '1', 10);
        expect($added)->toBe('123456789012345678901234567891.1234567890');
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalNonNegativeTest extends DecimalNonNegative
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

describe('Throwing static', function () {
    it('DecimalNonNegative::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalNonNegativeTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonNegativeTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonNegativeTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonNegativeTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonNegativeTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalNonNegativeTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
