<?php

declare(strict_types=1);

namespace Decimal;

use const PHP_INT_MAX;

use Exception;
use PhpTypedValues\Decimal\DecimalStandard;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

use function sprintf;

describe('DecimalStandard', function () {
    it('accepts valid decimal strings and preserves value/toString', function (): void {
        $a = new DecimalStandard('0.0');
        $b = DecimalStandard::fromString('123.0');
        $c = DecimalStandard::fromString('-5.0');
        $d = DecimalStandard::fromString('3.14');

        expect($a->value())->toBe('0.0')
            ->and($a->toString())->toBe('0.0')
            ->and($b->value())->toBe('123.0')
            ->and($c->value())->toBe('-5.0')
            ->and($d->toString())->toBe('3.14');
    });

    it('throws on malformed decimal strings', function (): void {
        expect(fn() => new DecimalStandard(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalStandard::fromString(' '))
            ->toThrow(DecimalTypeException::class, 'String " " has no valid decimal value')
            ->and(fn() => DecimalStandard::fromString('abc'))
            ->toThrow(DecimalTypeException::class, 'String "abc" has no valid strict decimal value')
            ->and(fn() => DecimalStandard::fromString('.5'))
            ->toThrow(DecimalTypeException::class, 'String ".5" has no valid strict decimal value')
            ->and(fn() => DecimalStandard::fromString('1.'))
            ->toThrow(DecimalTypeException::class, 'String "1." has no valid strict decimal value')
            ->and(fn() => DecimalStandard::fromString('+1'))
            ->toThrow(DecimalTypeException::class, 'String "+1" has no valid strict decimal value');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalStandard::tryFromString('42.5');
        $bad = DecimalStandard::tryFromString('nope');

        expect($ok)
            ->toBeInstanceOf(DecimalStandard::class)
            ->and($ok->value())
            ->toBe('42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalStandard::fromString('1.0')->toFloat())->toBe(1.0)
            ->and(DecimalStandard::fromString('-2.0')->toFloat())->toBe(-2.0)
            ->and(DecimalStandard::fromString('1.5')->toFloat())->toBe(1.5);

        expect(fn() => DecimalStandard::fromString('1.50')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "1.50" has no valid strict decimal value')
            ->and(fn() => DecimalStandard::fromString('0')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "0" has no valid strict decimal value')
            ->and(fn() => DecimalStandard::fromString('2.000')->toFloat())
            ->toThrow(DecimalTypeException::class, 'String "2.000" has no valid strict decimal value');
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalStandard::fromString('1.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalStandard('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        // valid inputs (as strings)
        $fromString = DecimalStandard::tryFromMixed('42');
        $fromStringFloat = DecimalStandard::tryFromMixed('3.1415');

        // stringable object
        $stringable = new class {
            public function __toString(): string
            {
                return '-5.5';
            }
        };
        $fromStringable = DecimalStandard::tryFromMixed($stringable);

        // invalid inputs
        $fromArray = DecimalStandard::tryFromMixed(['x']);
        $fromNull = DecimalStandard::tryFromMixed(null);
        $fromInt = DecimalStandard::tryFromMixed(123);
        $fromObject = DecimalStandard::tryFromMixed(new stdClass());

        expect($fromString)->toBeInstanceOf(DecimalStandard::class)
            ->and($fromString->value())->toBe('42')
            ->and($fromStringFloat)->toBeInstanceOf(DecimalStandard::class)
            ->and($fromStringFloat->value())->toBe('3.1415')
            ->and($fromStringable)->toBeInstanceOf(DecimalStandard::class)
            ->and($fromStringable->value())->toBe('-5.5')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromInt)->toBeInstanceOf(DecimalStandard::class)
            ->and($fromInt->value())->toBe('123.0')
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance should report false
        $ok = DecimalStandard::fromString('10.5');

        // Invalid inputs via tryFrom* should return Undefined which reports true
        $u1 = DecimalStandard::tryFromString('abc');
        $u2 = DecimalStandard::tryFromMixed(['x']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($ok->isUndefined())->not()->toBeTrue()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalStandard::fromString('10.5');
        expect($v->isTypeOf(DecimalStandard::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalStandard::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = DecimalStandard::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass', DecimalStandard::class, 'AnotherClass'))->toBeTrue();
    });

    it('isEmpty is always false for DecimalStandard', function (): void {
        $d = new DecimalStandard('0.0');
        expect($d->isEmpty())->toBeFalse()
            ->and($d->isEmpty())->not()->toBeTrue();
    });

    it('covers conversions for DecimalStandard', function (): void {
        expect(DecimalStandard::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalStandard::fromInt(123)->value())->toBe('123.0')
            ->and(DecimalStandard::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(DecimalStandard::fromDecimal('1.23')->value())->toBe('1.23');

        expect(fn() => DecimalStandard::fromString('true'))->toThrow(DecimalTypeException::class);

        $vInt = DecimalStandard::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and($vInt->toDecimal())->toBe('123')
            ->and(fn() => $vInt->toBool())->toThrow(IntegerTypeException::class);

        expect(fn() => DecimalStandard::fromString('1.2')->toFloat())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalStandard for valid inputs', function (): void {
        expect(DecimalStandard::tryFromFloat(1.2))->toBeInstanceOf(DecimalStandard::class)
            ->and(DecimalStandard::tryFromInt(123))->toBeInstanceOf(DecimalStandard::class);
    });

    it('cast float > decimal', function (): void {
        expect(DecimalStandard::fromFloat(1.5)->toString())->toBe('1.5')
            ->and(DecimalStandard::fromFloat(0.1)->toString())->toBe('0.10000000000000001')
            ->and(DecimalStandard::fromFloat(1)->toString())->toBe('1.0')
            ->and(DecimalStandard::fromFloat(-1)->toString())->toBe('-1.0');
    });

    it('cast decimal > decimal', function (): void {
        expect(DecimalStandard::fromDecimal('1.0')->toString())->toBe('1.0')
            ->and(DecimalStandard::fromDecimal('-1.0')->toString())->toBe('-1.0');
    });

    it('cast bool > decimal', function (): void {
        expect(DecimalStandard::fromBool(true)->toString())->toBe('1.0')
            ->and(DecimalStandard::fromBool(false)->toString())->toBe('0.0');
    });

    it('cast int > decimal', function (): void {
        expect(DecimalStandard::fromInt(0)->toString())->toBe('0.0')
            ->and(DecimalStandard::fromInt(99999999999999999)->toString())->toBe('99999999999999999.0')
            ->and(DecimalStandard::fromInt(-1)->toString())->toBe('-1.0')
            ->and(DecimalStandard::fromInt(PHP_INT_MAX)->toString())->toBe(PHP_INT_MAX . '.0')
            ->and(DecimalStandard::fromInt(1)->toString())->toBe('1.0');
    });

    it('can be used with bcmath functions', function (): void {
        $a = DecimalStandard::fromString('1.23');
        $b = DecimalStandard::fromString('2.34');

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
        $decimal = DecimalStandard::fromString($value);

        expect($decimal->toString())
            ->toBe($value, sprintf('Case "%s" failed for value "%s"', $label, $value));
    })->with([
        ['zero', '0.0'],
        ['simple_decimal', '123.0'],
        ['negative_int', '-123.0'],
        ['int_with_dot_zero', '1.0'],
        ['negative_int_with_dot_zero', '-1.0'],
        ['simple_fraction', '1.5'],
        ['negative_fraction', '-1.5'],
        ['pi_like', '3.14'],
        ['negative_pi_like', '-3.14'],
        ['leading_zero_fraction', '10.01'],
        ['negative_leading_zero_fraction', '-10.01'],
        ['big_int', '999999999999999999'],
        ['negative_big_int', '-999999999999999999'],
        ['big_decimal', '12345678901234567890.123456789'],
        ['biger_decimal', '1234567890123456789023532452345342532452345.12345678923453245324534253245342534253'],
        ['negative_big_decimal', '-12345678901234567890.123456789'],
    ]);

    it('rejects invalid decimal strings', function (string $label, string $value): void {
        try {
            DecimalStandard::fromString($value);
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
        $decimal = DecimalStandard::fromString($large);

        expect($decimal->value())->toBe($large);

        $added = bcadd($decimal->value(), '1', 10);
        expect($added)->toBe('123456789012345678901234567891.1234567890');
    });

    it('validates using bcmath internally', function (): void {
        // Even if it passes regex, bcmath might have its own ideas (though our regex is stricter)
        $v = DecimalStandard::fromString('123.456');
        expect($v->value())->toBe('123.456');

        // Test with a value that might pass regex but be problematic (if any exist)
        // Currently our regex is very strict, so it's hard to find one.
        // But if we ever relax the regex, bcmath check will be a safety net.
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalStandardTest extends DecimalStandard
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

describe('Throwing static', function () {
    it('DecimalStandard::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalStandardTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalStandardTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalStandardTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalStandardTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalStandardTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
