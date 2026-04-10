<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Float;

use const INF;
use const NAN;

use Exception;
use PhpTypedValues\Exception\Float\NonZeroFloatTypeException;
use PhpTypedValues\Float\FloatNonZero;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(FloatNonZero::class);

describe('FloatNonZero', function () {
    it('accepts valid non-zero float values', function (float $input) {
        $v = new FloatNonZero($input);
        expect($v->value())->toBe($input);
    })->with([
        'positive' => [1.5],
        'negative' => [-1.5],
    ]);

    it('throws on zero values', function (float $input) {
        expect(fn() => new FloatNonZero($input))
            ->toThrow(NonZeroFloatTypeException::class);
    })->with([
        'zero' => [0.0],
        '-zero' => [-0.0],
    ]);

    it('throws on INF or NAN', function (float $input) {
        expect(fn() => new FloatNonZero($input))
            ->toThrow(NonZeroFloatTypeException::class);
    })->with([
        'INF' => [INF],
        'NAN' => [NAN],
    ]);

    it('throws on fromNull and toNull', function () {
        expect(fn() => FloatNonZero::fromNull(null))->toThrow(NonZeroFloatTypeException::class, 'Float type cannot be created from null')
            ->and(fn() => FloatNonZero::toNull())->toThrow(NonZeroFloatTypeException::class, 'Float type cannot be converted to null');
    });

    it('creates from mixed types', function () {
        expect(FloatNonZero::fromBool(true)->value())->toBe(1.0)
            ->and(FloatNonZero::fromDecimal('1.5')->value())->toBe(1.5)
            ->and(FloatNonZero::fromFloat(2.5)->value())->toBe(2.5)
            ->and(FloatNonZero::fromInt(10)->value())->toBe(10.0)
            ->and(FloatNonZero::fromString('3.5')->value())->toBe(3.5);
    });

    it('tryFromMixed handles various inputs', function (mixed $input, bool $isValid) {
        $result = FloatNonZero::tryFromMixed($input);
        if ($isValid) {
            expect($result)->toBeInstanceOf(FloatNonZero::class);
        } else {
            expect($result)->toBeInstanceOf(Undefined::class);
        }
    })->with([
        'valid float' => [1.5, true],
        'zero float' => [0.0, false],
        'valid int' => [1, true],
        'zero int' => [0, false],
        'true bool' => [true, true],
        'false bool' => [false, false],
        'valid string' => ['1.5', true],
        'stringable' => [new class implements Stringable {
            public function __toString(): string
            {
                return '1.5';
            }
        }, true],
        'zero string' => ['0.0', false],
        'invalid string' => ['abc', false],
        'array' => [[], false],
        'object' => [new stdClass(), false],
    ]);

    it('has standard type methods', function () {
        $v = new FloatNonZero(1.0);
        expect($v->isEmpty())->toBeFalse()
            ->and($v->isUndefined())->toBeFalse()
            ->and($v->isTypeOf(FloatNonZero::class))->toBeTrue()
            ->and($v->isTypeOf(stdClass::class))->toBeFalse()
            ->and($v->jsonSerialize())->toBe(1.0)
            ->and($v->value())->toBe(1.0);
    });

    it('converts to other types', function () {
        $v = new FloatNonZero(1.0);
        expect($v->toBool())->toBeTrue()
            ->and($v->toDecimal())->toBe('1.0')
            ->and($v->toFloat())->toBe(1.0)
            ->and($v->toInt())->toBe(1)
            ->and($v->toString())->toBe('1.0');
    });

    it('tryFrom* return Undefined on failure', function () {
        expect(FloatNonZero::tryFromBool(false))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZero::tryFromDecimal('0.0'))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZero::tryFromFloat(0.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZero::tryFromInt(0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZero::tryFromString('0.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class FloatNonZeroTest extends FloatNonZero
{
    public function __construct(float $value)
    {
        throw new Exception('test');
    }
}

describe('FloatNonZero coverage', function () {
    it('kills tryFromMixed catch block', function () {
        expect(FloatNonZeroTest::tryFromMixed(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZeroTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZeroTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZeroTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZeroTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonZeroTest::tryFromString('1.0'))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $v = new FloatNonZero(1.0);
        expect($v->isTypeOf('NonExistentClass', FloatNonZero::class, 'AnotherClass'))->toBeTrue();
    });
});
