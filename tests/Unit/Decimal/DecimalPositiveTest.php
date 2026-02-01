<?php

declare(strict_types=1);

namespace Decimal;

use const PHP_INT_MAX;

use Exception;
use PhpTypedValues\Decimal\DecimalPositive;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

use function sprintf;

describe('DecimalPositive', function () {
    it('accepts valid positive decimal strings and preserves value/toString', function (): void {
        $a = new DecimalPositive('0.1');
        $b = DecimalPositive::fromString('123.0');
        $c = DecimalPositive::fromString('3.14');

        expect($a->value())->toBe('0.1')
            ->and($a->toString())->toBe('0.1')
            ->and($b->value())->toBe('123.0')
            ->and($c->toString())->toBe('3.14');
    });

    it('throws on non-positive or malformed decimal strings', function (): void {
        expect(fn() => new DecimalPositive(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalPositive::fromString(' '))
            ->toThrow(DecimalTypeException::class, 'String " " has no valid decimal value')
            ->and(fn() => DecimalPositive::fromString('abc'))
            ->toThrow(DecimalTypeException::class, 'String "abc" has no valid strict decimal value')
            ->and(fn() => DecimalPositive::fromString('.5'))
            ->toThrow(DecimalTypeException::class, 'String ".5" has no valid strict decimal value')
            ->and(fn() => DecimalPositive::fromString('1.'))
            ->toThrow(DecimalTypeException::class, 'String "1." has no valid strict decimal value')
            ->and(fn() => DecimalPositive::fromString('+1'))
            ->toThrow(DecimalTypeException::class, 'String "+1" has no valid strict decimal value')
            ->and(fn() => DecimalPositive::fromString('0.0'))
            ->toThrow(DecimalTypeException::class, 'Decimal "0.0" is not a positive value')
            ->and(fn() => DecimalPositive::fromString('-1.0'))
            ->toThrow(DecimalTypeException::class, 'Decimal "-1.0" is not a positive value');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalPositive::tryFromString('42.5');
        $bad = DecimalPositive::tryFromString('nope');
        $zero = DecimalPositive::tryFromString('0.0');

        expect($ok)
            ->toBeInstanceOf(DecimalPositive::class)
            ->and($ok->value())
            ->toBe('42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($zero)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalPositive::fromString('1.0')->toFloat())->toBe(1.0)
            ->and(DecimalPositive::fromString('1.5')->toFloat())->toBe(1.5);

        expect(fn() => DecimalPositive::fromString('1.50')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "1.50" has no valid strict decimal value')
            ->and(fn() => DecimalPositive::fromString('2.000')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "2.000" has no valid strict decimal value');
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalPositive::fromString('1.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalPositive('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs (as strings)
        $fromString = DecimalPositive::tryFromMixed('42');
        $fromStringFloat = DecimalPositive::tryFromMixed('3.1415');

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '5.5';
            }
        };
        $fromStringable = DecimalPositive::tryFromMixed($stringable);

        // invalid inputs
        $fromArray = DecimalPositive::tryFromMixed(['x']);
        $fromNull = DecimalPositive::tryFromMixed(null);
        $fromInt = DecimalPositive::tryFromMixed(-123);
        $fromObject = DecimalPositive::tryFromMixed(new stdClass());

        expect($fromString)->toBeInstanceOf(DecimalPositive::class)
            ->and($fromString->value())->toBe('42')
            ->and($fromStringFloat)->toBeInstanceOf(DecimalPositive::class)
            ->and($fromStringFloat->value())->toBe('3.1415')
            ->and($fromStringable)->toBeInstanceOf(DecimalPositive::class)
            ->and($fromStringable->value())->toBe('5.5')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromInt)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = DecimalPositive::fromString('10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = DecimalPositive::tryFromString('abc');
        $u2 = DecimalPositive::tryFromMixed(['x']);
        $u3 = DecimalPositive::tryFromInt(0);

        expect($ok->isUndefined())->toBeFalse()
            ->and($ok->isUndefined())->not()->toBeTrue()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue()
            ->and($u3->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalPositive::fromString('10.5');
        expect($v->isTypeOf(DecimalPositive::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalPositive::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = DecimalPositive::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass', DecimalPositive::class, 'AnotherClass'))->toBeTrue();
    });

    it('isEmpty is always false for DecimalPositive', function (): void {
        $d = new DecimalPositive('1.0');
        expect($d->isEmpty())->toBeFalse()
            ->and($d->isEmpty())->not()->toBeTrue();
    });

    it('covers conversions for DecimalPositive', function (): void {
        expect(DecimalPositive::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalPositive::fromInt(123)->value())->toBe('123.0')
            ->and(DecimalPositive::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(DecimalPositive::fromDecimal('1.23')->value())->toBe('1.23');

        expect(fn() => DecimalPositive::fromString('true'))->toThrow(DecimalTypeException::class);

        $vInt = DecimalPositive::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and($vInt->toDecimal())->toBe('123')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);

        expect(fn() => DecimalPositive::fromString('1.2')->toFloat())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalPositive for valid inputs', function (): void {
        expect(DecimalPositive::tryFromFloat(1.2))->toBeInstanceOf(DecimalPositive::class)
            ->and(DecimalPositive::tryFromInt(123))->toBeInstanceOf(DecimalPositive::class)
            ->and(DecimalPositive::tryFromBool(true))->toBeInstanceOf(DecimalPositive::class);
    });

    it('cast float > decimal', function (): void {
        expect(DecimalPositive::fromFloat(1.5)->toString())->toBe('1.5')
            ->and(DecimalPositive::fromFloat(0.1)->toString())->toBe('0.10000000000000001')
            ->and(DecimalPositive::fromFloat(1)->toString())->toBe('1.0');

        expect(fn() => DecimalPositive::fromFloat(0.0))->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalPositive::fromFloat(-1.0))->toThrow(DecimalTypeException::class);
    });

    it('cast decimal > decimal', function (): void {
        expect(DecimalPositive::fromDecimal('1.0')->toString())->toBe('1.0');

        expect(fn() => DecimalPositive::fromDecimal('0.0'))->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalPositive::fromDecimal('-1.0'))->toThrow(DecimalTypeException::class);
    });

    it('cast bool > decimal', function (): void {
        expect(DecimalPositive::fromBool(true)->toString())->toBe('1.0');

        expect(fn() => DecimalPositive::fromBool(false))->toThrow(DecimalTypeException::class);
    });

    it('cast int > decimal', function (): void {
        expect(DecimalPositive::fromInt(123)->toString())->toBe('123.0')
            ->and(DecimalPositive::fromInt(99999999999999999)->toString())->toBe('99999999999999999.0')
            ->and(DecimalPositive::fromInt(PHP_INT_MAX)->toString())->toBe(PHP_INT_MAX . '.0')
            ->and(DecimalPositive::fromInt(1)->toString())->toBe('1.0');

        expect(fn() => DecimalPositive::fromInt(0))->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalPositive::fromInt(-1))->toThrow(DecimalTypeException::class);
    });

    it('can be used with bcmath functions', function (): void {
        $a = DecimalPositive::fromString('1.23');
        $b = DecimalPositive::fromString('2.34');

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
        $decimal = DecimalPositive::fromString($value);

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
    ]);

    it('rejects invalid decimal strings', function (string $label, string $value): void {
        try {
            DecimalPositive::fromString($value);
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
        ['zero_decimal', '0.0'],
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
        $decimal = DecimalPositive::fromString($large);

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
readonly class DecimalPositiveTest extends DecimalPositive
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
    it('DecimalPositive::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalPositiveTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPositiveTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPositiveTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPositiveTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPositiveTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPositiveTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
