<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Decimal\Specific;

use Exception;
use PhpTypedValues\Decimal\Specific\DecimalMoney;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

describe('DecimalMoney', function () {
    it('accepts valid non-negative decimal strings with 2 decimal places and preserves value/toString', function (): void {
        $a = new DecimalMoney('0.10');
        $b = DecimalMoney::fromString('123.00');
        $c = DecimalMoney::fromString('3.14');
        $d = DecimalMoney::fromString('0.00');

        expect($a->value())->toBe('0.10')
            ->and($a->toString())->toBe('0.10')
            ->and($b->value())->toBe('123.00')
            ->and($c->toString())->toBe('3.14')
            ->and($d->value())->toBe('0.00');
    });

    it('rejects empty string with DecimalTypeException', function (): void {
        expect(fn() => new DecimalMoney(''))->toThrow(DecimalTypeException::class);
    });

    it('throws on negative or malformed decimal strings or invalid decimal places', function (): void {
        expect(fn() => new DecimalMoney(''))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString(' '))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('abc'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('.5'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('1.'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('+1'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('-1.00'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('1.0'))
            ->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromString('1.000'))
            ->toThrow(DecimalTypeException::class);
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalMoney::tryFromString('42.50');
        $bad = DecimalMoney::tryFromString('nope');
        $wrongPlaces = DecimalMoney::tryFromString('42.5');

        expect($ok)
            ->toBeInstanceOf(DecimalMoney::class)
            ->and($ok->value())
            ->toBe('42.50')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($wrongPlaces)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float only when string equals (string)(float) cast', function (): void {
        expect(DecimalMoney::fromString('1.00')->toFloat())->toBe(1.0)
            ->and(DecimalMoney::fromString('1.50')->toFloat())->toBe(1.5)
            ->and(DecimalMoney::fromString('0.00')->toFloat())->toBe(0.0)
            ->and(fn() => DecimalMoney::fromString('1.51')->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => DecimalMoney::fromString(''))->toThrow(DecimalTypeException::class)
            ->and(fn() => new DecimalMoney(''))->toThrow(DecimalTypeException::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalMoney::fromString('1.10')->jsonSerialize())->toBeString();
    });

    it('__toString returns the original decimal string', function (): void {
        $d = new DecimalMoney('3.14');
        expect((string) $d)->toBe('3.14')
            ->and($d->__toString())->toBe('3.14');
    });

    it('tryFromMixed handles valid decimal-like values and invalid mixed inputs', function (): void {
        $fromString = DecimalMoney::tryFromMixed('42.00');
        $fromStringFloat = DecimalMoney::tryFromMixed('3.14');

        // invalid inputs
        $fromArray = DecimalMoney::tryFromMixed(['x']);
        $fromNull = DecimalMoney::tryFromMixed(null);
        $fromInt = DecimalMoney::tryFromMixed(-123);
        $fromObject = DecimalMoney::tryFromMixed(new stdClass());

        expect($fromString)->toBeInstanceOf(DecimalMoney::class)
            ->and($fromString->value())->toBe('42.00')
            ->and($fromStringFloat)->toBeInstanceOf(DecimalMoney::class)
            ->and($fromStringFloat->value())->toBe('3.14')
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromInt)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = DecimalMoney::fromString('10.50');

        $u1 = DecimalMoney::tryFromString('abc');
        $u2 = DecimalMoney::tryFromMixed(['x']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    // Mutant killer: Line 56 - EmptyStringToNotEmpty (ID: 933dc702)
    // Ensures empty string check is actually validated
    it('throws DecimalTypeException for empty string input', function (): void {
        expect(fn() => new DecimalMoney(''))->toThrow(DecimalTypeException::class);
    });

    // Mutant killer: Line 65 - RemoveMethodCall (ID: 8e482b30)
    // Value "00.10" passes the money regex but fails stringToDecimal validation (leading zero on integer part > 0)
    it('throws when value passes money format but fails strict decimal validation', function (): void {
        expect(fn() => new DecimalMoney('00.10'))->toThrow(DecimalTypeException::class);
    });

    // Mutant killer: Line 315 - Stringable handling (ID: e8ce9c9200 and 6051c54b19)
    // Tests that Stringable objects are properly converted via (string) cast
    it('tryFromMixed handles Stringable objects correctly', function (): void {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '99.99';
            }
        };

        $result = DecimalMoney::tryFromMixed($stringable);

        expect($result)->toBeInstanceOf(DecimalMoney::class)
            ->and($result->value())->toBe('99.99');
    });

    it('isTypeOf returns true when class matches and false otherwise', function (): void {
        $v = DecimalMoney::fromString('10.50');
        expect($v->isTypeOf(DecimalMoney::class))->toBeTrue()
            ->and($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for DecimalMoney', function (): void {
        $d = new DecimalMoney('1.00');
        expect($d->isEmpty())->toBeFalse();
    });

    it('covers conversions for DecimalMoney', function (): void {
        expect(DecimalMoney::fromBool(true)->value())->toBe('1.00')
            ->and(DecimalMoney::fromInt(123)->value())->toBe('123.00')
            ->and(DecimalMoney::fromDecimal('1.20')->value())->toBe('1.20');

        $vInt = DecimalMoney::fromString('123.00');
        expect($vInt->toInt())->toBe(123)
            ->and($vInt->toDecimal())->toBe('123.0')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalMoney for valid inputs', function (): void {
        expect(DecimalMoney::tryFromFloat(1.5))->toBeInstanceOf(DecimalMoney::class)
            ->and(DecimalMoney::tryFromInt(123))->toBeInstanceOf(DecimalMoney::class)
            ->and(DecimalMoney::tryFromBool(true))->toBeInstanceOf(DecimalMoney::class);
    });

    it('cast float > decimal', function (): void {
        expect(DecimalMoney::fromFloat(1.5)->toString())->toBe('1.50')
            ->and(DecimalMoney::fromFloat(0.0)->toString())->toBe('0.00')
            ->and(fn() => DecimalMoney::fromFloat(-1.0))->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalMoney::fromFloat(1.234))->toThrow(DecimalTypeException::class);
    });

    it('cast decimal > decimal', function (): void {
        expect(DecimalMoney::fromDecimal('1.00')->toString())->toBe('1.00')
            ->and(DecimalMoney::fromDecimal('0.00')->toString())->toBe('0.00')
            ->and(fn() => DecimalMoney::fromDecimal('-1.00'))->toThrow(DecimalTypeException::class);
    });

    it('cast bool > decimal', function (): void {
        expect(DecimalMoney::fromBool(true)->toString())->toBe('1.00')
            ->and(DecimalMoney::fromBool(false)->toString())->toBe('0.00');
    });

    it('cast int > decimal', function (): void {
        expect(DecimalMoney::fromInt(123)->toString())->toBe('123.00')
            ->and(DecimalMoney::fromInt(0)->toString())->toBe('0.00')
            ->and(fn() => DecimalMoney::fromInt(-1))->toThrow(DecimalTypeException::class);
    });

    it('can be used with bcmath functions', function (): void {
        $a = DecimalMoney::fromString('1.23');
        $b = DecimalMoney::fromString('2.34');

        $sum = bcadd($a->value(), $b->value(), 2);
        expect($sum)->toBe('3.57');
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalMoneyTest extends DecimalMoney
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

describe('Throwing static money', function () {
    it('DecimalMoney::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalMoneyTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalMoneyTest::tryFromDecimal('1.00'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalMoneyTest::tryFromFloat(1.10))->toBeInstanceOf(Undefined::class)
            ->and(DecimalMoneyTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalMoneyTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalMoneyTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
