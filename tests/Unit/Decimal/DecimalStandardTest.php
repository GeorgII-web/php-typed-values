<?php

declare(strict_types=1);

namespace Decimal;

use Exception;
use PhpTypedValues\Decimal\DecimalStandard;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('DecimalStandard', function () {
    it('accepts valid decimal strings and preserves value/toString', function (): void {
        $a = new DecimalStandard('0');
        $b = DecimalStandard::fromString('123');
        $c = DecimalStandard::fromString('-5');
        $d = DecimalStandard::fromString('3.14');

        expect($a->value())->toBe('0')
            ->and($a->toString())->toBe('0')
            ->and($b->value())->toBe('123')
            ->and($c->value())->toBe('-5')
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
            ->toThrow(StringTypeException::class, 'String "1.50" has no valid strict float value')
            ->and(fn() => DecimalStandard::fromString('0')->toFloat())
            ->toThrow(StringTypeException::class, 'String "0" has no valid strict float value')
            ->and(fn() => DecimalStandard::fromString('2.000')->toFloat())
            ->toThrow(StringTypeException::class, 'String "2.000" has no valid strict float value');
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
            ->and($fromInt->value())->toBe('123')
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
        $d = new DecimalStandard('0');
        expect($d->isEmpty())->toBeFalse()
            ->and($d->isEmpty())->not()->toBeTrue();
    });

    it('covers conversions for DecimalStandard', function (): void {
        expect(fn() => DecimalStandard::fromBool(true))->toThrow(DecimalTypeException::class)
            ->and(fn() => DecimalStandard::fromBool(false))->toThrow(DecimalTypeException::class)
            ->and(DecimalStandard::fromInt(123)->value())->toBe('123')
            ->and(DecimalStandard::fromFloat(1.2)->value())->toBe('1.19999999999999996');

        expect(fn() => DecimalStandard::fromString('true'))->toThrow(DecimalTypeException::class);

        $vInt = DecimalStandard::fromString('123');
        expect($vInt->toInt())->toBe(123)
            ->and(fn() => $vInt->toBool())->toThrow(IntegerTypeException::class);

        expect(fn() => DecimalStandard::fromString('1.2')->toFloat())->toThrow(StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return DecimalStandard for valid inputs', function (): void {
        expect(DecimalStandard::tryFromFloat(1.2))->toBeInstanceOf(DecimalStandard::class)
            ->and(DecimalStandard::tryFromInt(123))->toBeInstanceOf(DecimalStandard::class);
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
