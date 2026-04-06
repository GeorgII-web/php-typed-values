<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Float\Specific;

use const INF;
use const NAN;

use Exception;
use PhpTypedValues\Exception\Float\PercentFloatTypeException;
use PhpTypedValues\Float\Specific\FloatPercent;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(FloatPercent::class);

describe('FloatPercent', function () {
    it('accepts valid percent float values', function (float $input) {
        $v = new FloatPercent($input);
        expect($v->value())->toBe($input);
    })->with([
        '0.0' => [0.0],
        '50.0' => [50.0],
        '100.0' => [100.0],
    ]);

    it('throws on out of range values', function (float $input) {
        expect(fn() => new FloatPercent($input))
            ->toThrow(PercentFloatTypeException::class);
    })->with([
        'negative' => [-0.1],
        'too large' => [100.1],
    ]);

    it('throws on INF or NAN', function (float $input) {
        expect(fn() => new FloatPercent($input))
            ->toThrow(PercentFloatTypeException::class);
    })->with([
        'INF' => [INF],
        'NAN' => [NAN],
    ]);

    it('creates from mixed types', function () {
        expect(FloatPercent::fromBool(true)->value())->toBe(1.0)
            ->and(FloatPercent::fromDecimal('1.5')->value())->toBe(1.5)
            ->and(FloatPercent::fromFloat(2.5)->value())->toBe(2.5)
            ->and(FloatPercent::fromInt(10)->value())->toBe(10.0)
            ->and(FloatPercent::fromString('3.5')->value())->toBe(3.5);
    });

    it('tryFromMixed handles various inputs', function (mixed $input, bool $isValid) {
        $result = FloatPercent::tryFromMixed($input);
        if ($isValid) {
            expect($result)->toBeInstanceOf(FloatPercent::class);
        } else {
            expect($result)->toBeInstanceOf(Undefined::class);
        }
    })->with([
        'valid float' => [1.5, true],
        'out of range float' => [100.5, false],
        'valid int' => [1, true],
        'out of range int' => [150, false],
        'true bool' => [true, true],
        'false bool' => [false, true],
        'valid string' => ['1.5', true],
        'stringable' => [new class implements Stringable {
            public function __toString(): string
            {
                return '1.5';
            }
        }, true],
        'invalid string' => ['abc', false],
        'array' => [[], false],
        'object' => [new stdClass(), false],
    ]);

    it('has standard type methods', function () {
        $v = new FloatPercent(1.0);
        expect($v->isEmpty())->toBeFalse()
            ->and($v->isUndefined())->toBeFalse()
            ->and($v->isTypeOf(FloatPercent::class))->toBeTrue()
            ->and($v->isTypeOf(stdClass::class))->toBeFalse()
            ->and($v->jsonSerialize())->toBe(1.0)
            ->and($v->value())->toBe(1.0);
    });

    it('converts to other types', function () {
        $v = new FloatPercent(1.0);
        expect($v->toBool())->toBeTrue()
            ->and($v->toDecimal())->toBe('1.0')
            ->and($v->toFloat())->toBe(1.0)
            ->and($v->toInt())->toBe(1)
            ->and($v->toString())->toBe('1.0');
    });

    it('tryFrom* return Undefined on failure', function () {
        expect(FloatPercent::tryFromDecimal('100.1'))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercent::tryFromFloat(100.1))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercent::tryFromInt(101))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercent::tryFromString('100.1'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class FloatPercentTest extends FloatPercent
{
    public function __construct(float $value)
    {
        throw new Exception('test');
    }
}

describe('FloatPercent coverage', function () {
    it('kills tryFromMixed catch block', function () {
        expect(FloatPercentTest::tryFromMixed(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercentTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercentTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercentTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercentTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(FloatPercentTest::tryFromString('1.0'))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $v = new FloatPercent(1.0);
        expect($v->isTypeOf('NonExistentClass', FloatPercent::class, 'AnotherClass'))->toBeTrue();
    });
});
