<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Decimal\Specific;

use Exception;
use PhpTypedValues\Decimal\Specific\DecimalProbability;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Decimal\ProbabilityDecimalTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DecimalProbability::class);

describe('DecimalProbability', function () {
    it('accepts valid probability decimal strings and preserves value/toString', function (): void {
        $a = new DecimalProbability('0.0');
        $b = DecimalProbability::fromString('1.0');
        $c = DecimalProbability::fromString('0.5');

        expect($a->value())->toBe('0.0')
            ->and($a->toString())->toBe('0.0')
            ->and($b->value())->toBe('1.0')
            ->and($c->toString())->toBe('0.5');
    });

    it('throws on out-of-range or malformed decimal strings', function (): void {
        expect(fn() => new DecimalProbability(''))
            ->toThrow(DecimalTypeException::class, 'String "" has no valid decimal value')
            ->and(fn() => DecimalProbability::fromString('-0.1'))
            ->toThrow(ProbabilityDecimalTypeException::class, 'Decimal "-0.1" is not a valid probability (0.0-1.0)')
            ->and(fn() => DecimalProbability::fromString('1.1'))
            ->toThrow(ProbabilityDecimalTypeException::class, 'Decimal "1.1" is not a valid probability (0.0-1.0)');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = DecimalProbability::tryFromString('0.42');
        $bad = DecimalProbability::tryFromString('nope');
        $out = DecimalProbability::tryFromString('1.1');

        expect($ok)
            ->toBeInstanceOf(DecimalProbability::class)
            ->and($ok->value())
            ->toBe('0.42')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class)
            ->and($out)
            ->toBeInstanceOf(Undefined::class);
    });

    it('toFloat returns exact float', function (): void {
        expect(DecimalProbability::fromString('1.0')->toFloat())->toBe(1.0)
            ->and(DecimalProbability::fromString('0.0')->toFloat())->toBe(0.0);
    });

    it('jsonSerialize returns string', function (): void {
        expect(DecimalProbability::fromString('0.1')->jsonSerialize())->toBeString();
    });

    it('__toString returns original string', function (): void {
        $d = new DecimalProbability('0.314');
        expect((string) $d)->toBe('0.314');
    });

    it('tryFromMixed handles valid values', function (): void {
        expect(DecimalProbability::tryFromMixed('0.5'))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromMixed(1))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromMixed(0.123))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromMixed(true))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromMixed(false))->toBeInstanceOf(DecimalProbability::class);

        $stringable = new class {
            public function __toString(): string
            {
                return '0.75';
            }
        };
        expect(DecimalProbability::tryFromMixed($stringable))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromMixed(['x']))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbability::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbability::tryFromMixed(-0.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbability::tryFromMixed(1.1))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = DecimalProbability::fromString('0.5');
        expect($v->isTypeOf(DecimalProbability::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = DecimalProbability::fromString('0.5');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isUndefined returns false for instances', function (): void {
        $ok = DecimalProbability::fromString('0.5');
        expect($ok->isUndefined())->toBeFalse();
    });

    it('isEmpty is always false for DecimalProbability', function (): void {
        $d = new DecimalProbability('1.0');
        expect($d->isEmpty())->toBeFalse();
    });

    it('covers conversions for DecimalProbability', function (): void {
        expect(DecimalProbability::fromBool(true)->value())->toBe('1.0')
            ->and(DecimalProbability::fromInt(0)->value())->toBe('0.0')
            ->and(DecimalProbability::fromFloat(0.5)->value())->toBe('0.5')
            ->and(DecimalProbability::fromDecimal('0.23')->value())->toBe('0.23');

        $vInt = DecimalProbability::fromString('0.0');
        expect(fn() => $vInt->toInt())->toThrow(StringTypeException::class)
            ->and($vInt->toDecimal())->toBe('0.0')
            ->and(fn() => $vInt->toBool())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalProbability for valid inputs', function (): void {
        expect(DecimalProbability::tryFromFloat(0.2))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromInt(0))->toBeInstanceOf(DecimalProbability::class)
            ->and(DecimalProbability::tryFromBool(true))->toBeInstanceOf(DecimalProbability::class);
    });

    it('kills scale mutants with extreme precision', function (): void {
        $val20neg = '-0.' . str_repeat('0', 25) . '1';
        expect(DecimalProbability::fromString($val20neg))->toBeInstanceOf(DecimalProbability::class);

        $val19neg = '-0.' . str_repeat('0', 19) . '1';
        expect(fn() => DecimalProbability::fromString($val19neg))->toThrow(DecimalProbability::class);

        $val20pos = '1.' . str_repeat('0', 25) . '1';
        expect(DecimalProbability::fromString($val20pos))->toBeInstanceOf(DecimalProbability::class);

        $val19pos = '1.' . str_repeat('0', 19) . '1';
        expect(fn() => DecimalProbability::fromString($val19pos))->toThrow(DecimalProbability::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class DecimalProbabilityTest extends DecimalProbability
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

describe('Throwing static for DecimalProbability', function () {
    it('DecimalProbability::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(DecimalProbabilityTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbabilityTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbabilityTest::tryFromFloat(0.1))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbabilityTest::tryFromInt(0))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbabilityTest::tryFromMixed('0.23'))->toBeInstanceOf(Undefined::class)
            ->and(DecimalProbabilityTest::tryFromString('0.23'))->toBeInstanceOf(Undefined::class);
    });
});
