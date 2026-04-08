<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Decimal\Specific;

use Exception;
use PhpTypedValues\Decimal\Specific\DecimalPercent;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\PercentDecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DecimalPercent::class);

describe('DecimalPercent', function () {
    it('accepts valid percent decimal strings and preserves value/toString', function (): void {
        $a = new DecimalPercent('0.0');
        $b = DecimalPercent::fromString('100.0');
        $c = DecimalPercent::fromString('50.5');

        expect($a->value())->toBe('0.0')
            ->and($a->toString())->toBe('0.0')
            ->and($b->value())->toBe('100.0')
            ->and($c->toString())->toBe('50.5');
    });

    it('throws on out-of-range or malformed decimal strings', function (): void {
        expect(fn() => new DecimalPercent(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalPercent::fromString('-0.1'))
            ->toThrow(PercentDecimalTypeException::class, 'Decimal "-0.1" is not a valid percent (0.0-100.0)')
            ->and(fn() => DecimalPercent::fromString('100.1'))
            ->toThrow(PercentDecimalTypeException::class, 'Decimal "100.1" is not a valid percent (0.0-100.0)');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalPercent::tryFromString('42.5');
        $bad = DecimalPercent::tryFromString('nope');
        $out = DecimalPercent::tryFromString('101.0');

        expect($ok)
            ->toBeInstanceOf(DecimalPercent::class)
            ->and($ok->value())
            ->toBe('42.5')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($out)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float', function (): void {
        expect(DecimalPercent::fromString('100.0')->toFloat())->toBe(100.0)
            ->and(DecimalPercent::fromString('0.0')->toFloat())->toBe(0.0);
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalPercent::fromString('1.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns original string', function (): void {
        $d = new DecimalPercent('3.14');
        expect((string) $d)->toBe('3.14');
    });

    it('tryFromMixed handles valid values', function (): void {
        expect(DecimalPercent::tryFromMixed('50'))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromMixed(75))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromMixed(0.5))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromMixed(true))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromMixed(false))->toBeInstanceOf(DecimalPercent::class);

        $stringable = new class {
            public function __toString(): string
            {
                return '50.5';
            }
        };
        expect(DecimalPercent::tryFromMixed($stringable))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromMixed(['x']))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercent::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercent::tryFromMixed(-1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercent::tryFromMixed(101))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalPercent::fromString('10.5');
        expect($v->isTypeOf(DecimalPercent::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalPercent::fromString('10.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isUndefined returns false for instances', function (): void {
        $ok = DecimalPercent::fromString('10.5');
        expect($ok->isUndefined())->toBeFalse();
    });

    it('isEmpty is always false for DecimalPercent', function (): void {
        $d = new DecimalPercent('1.0');
        expect($d->isEmpty())->toBeFalse();
    });

    it('covers conversions for DecimalPercent', function (): void {
        expect(DecimalPercent::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalPercent::fromInt(100)->value())->toBe('100.0')
            ->and(DecimalPercent::fromFloat(0.5)->value())->toBe('0.5')
            ->and(DecimalPercent::fromDecimal('1.23')->value())->toBe('1.23')
            ->and(DecimalPercent::fromDecimal('0.00000000000000000000000000001')->value())->toBe('0.00000000000000000000000000001');

        $vInt = DecimalPercent::fromString('50');
        expect($vInt->toInt())->toBe(50)
            ->and($vInt->toDecimal())->toBe('50')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalPercent for valid inputs', function (): void {
        expect(DecimalPercent::tryFromFloat(1.2))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromInt(50))->toBeInstanceOf(DecimalPercent::class)
            ->and(DecimalPercent::tryFromBool(true))->toBeInstanceOf(DecimalPercent::class);
    });

    it('rejects extreme precision out of bounds values', function (): void {
        $val20neg = '-0.' . str_repeat('0', 25) . '1';
        expect(fn() => DecimalPercent::fromString($val20neg))->toThrow(PercentDecimalTypeException::class);

        $val19neg = '-0.' . str_repeat('0', 19) . '1';
        expect(fn() => DecimalPercent::fromString($val19neg))->toThrow(PercentDecimalTypeException::class);

        $val20pos = '100.' . str_repeat('0', 25) . '1';
        expect(fn() => DecimalPercent::fromString($val20pos))->toThrow(PercentDecimalTypeException::class);

        $val19pos = '100.' . str_repeat('0', 19) . '1';
        expect(fn() => DecimalPercent::fromString($val19pos))->toThrow(PercentDecimalTypeException::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalPercentTest extends DecimalPercent
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

describe('Throwing static for DecimalPercent', function () {
    it('DecimalPercent::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalPercentTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercentTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercentTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercentTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercentTest::tryFromMixed('1.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalPercentTest::tryFromString('1.23'))->toBeInstanceOf(Undefined::class);
    });
});
