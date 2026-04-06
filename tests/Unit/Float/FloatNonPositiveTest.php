<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Float;

use const INF;
use const NAN;

use Exception;
use PhpTypedValues\Exception\Float\NonPositiveFloatTypeException;
use PhpTypedValues\Float\FloatNonPositive;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(FloatNonPositive::class);

describe('FloatNonPositive', function () {
    it('accepts valid non-positive float values', function (float $input) {
        $v = new FloatNonPositive($input);
        expect($v->value())->toBe($input);
    })->with([
        'negative' => [-1.5],
        'zero' => [0.0],
    ]);

    it('throws on positive values', function (float $input) {
        expect(fn() => new FloatNonPositive($input))
            ->toThrow(NonPositiveFloatTypeException::class);
    })->with([
        'positive' => [1.5],
    ]);

    it('throws on INF or NAN', function (float $input) {
        expect(fn() => new FloatNonPositive($input))
            ->toThrow(NonPositiveFloatTypeException::class);
    })->with([
        'INF' => [INF],
        'NAN' => [NAN],
        '-INF' => [-INF],
    ]);

    it('creates from mixed types', function () {
        expect(FloatNonPositive::fromDecimal('-1.5')->value())->toBe(-1.5)
            ->and(FloatNonPositive::fromFloat(-2.5)->value())->toBe(-2.5)
            ->and(FloatNonPositive::fromInt(-10)->value())->toBe(-10.0)
            ->and(FloatNonPositive::fromString('-3.5')->value())->toBe(-3.5);
    });

    it('tryFromMixed handles various inputs', function (mixed $input, bool $isValid) {
        $result = FloatNonPositive::tryFromMixed($input);
        if ($isValid) {
            expect($result)->toBeInstanceOf(FloatNonPositive::class);
        } else {
            expect($result)->toBeInstanceOf(Undefined::class);
        }
    })->with([
        'valid float' => [-1.5, true],
        'positive float' => [1.5, false],
        'valid int' => [-1, true],
        'positive int' => [1, false],
        'true bool' => [true, false],
        'false bool' => [false, true],
        'valid string' => ['-1.5', true],
        'stringable' => [new class implements Stringable {
            public function __toString(): string
            {
                return '-1.5';
            }
        }, true],
        'positive string' => ['1.5', false],
        'invalid string' => ['abc', false],
        'array' => [[], false],
        'object' => [new stdClass(), false],
    ]);

    it('has standard type methods', function () {
        $v = new FloatNonPositive(-1.0);
        expect($v->isEmpty())->toBeFalse()
            ->and($v->isUndefined())->toBeFalse()
            ->and($v->isTypeOf(FloatNonPositive::class))->toBeTrue()
            ->and($v->isTypeOf(stdClass::class))->toBeFalse()
            ->and($v->jsonSerialize())->toBe(-1.0)
            ->and($v->value())->toBe(-1.0);
    });

    it('converts to other types', function () {
        $v = new FloatNonPositive(-1.0);
        expect($v->toDecimal())->toBe('-1.0')
            ->and($v->toFloat())->toBe(-1.0)
            ->and($v->toInt())->toBe(-1)
            ->and($v->toString())->toBe('-1.0');

        $vZero = new FloatNonPositive(0.0);
        expect($vZero->toBool())->toBeFalse();
    });

    it('tryFrom* return Undefined on failure', function () {
        expect(FloatNonPositive::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositive::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositive::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositive::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositive::tryFromString('1.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class FloatNonPositiveTest extends FloatNonPositive
{
    public function __construct(float $value)
    {
        throw new Exception('test');
    }
}

describe('FloatNonPositive coverage', function () {
    it('kills tryFromMixed catch block', function () {
        expect(FloatNonPositiveTest::tryFromMixed(-1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositiveTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositiveTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositiveTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositiveTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(FloatNonPositiveTest::tryFromString('1.0'))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $v = new FloatNonPositive(-1.0);
        expect($v->isTypeOf('NonExistentClass', FloatNonPositive::class, 'AnotherClass'))->toBeTrue();
    });
});
