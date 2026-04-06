<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Float\Specific;

use const INF;
use const NAN;

use Exception;
use PhpTypedValues\Exception\Float\ProbabilityFloatTypeException;
use PhpTypedValues\Float\Specific\FloatProbability;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(FloatProbability::class);

describe('FloatProbability', function () {
    it('accepts valid probability float values', function (float $input) {
        $v = new FloatProbability($input);
        expect($v->value())->toBe($input);
    })->with([
        '0.0' => [0.0],
        '0.5' => [0.5],
        '1.0' => [1.0],
    ]);

    it('throws on out of range values', function (float $input) {
        expect(fn() => new FloatProbability($input))
            ->toThrow(ProbabilityFloatTypeException::class);
    })->with([
        'negative' => [-0.00001],
        'too large' => [1.00001],
    ]);

    it('throws on INF or NAN', function (float $input) {
        expect(fn() => new FloatProbability($input))
            ->toThrow(ProbabilityFloatTypeException::class);
    })->with([
        'INF' => [INF],
        'NAN' => [NAN],
    ]);

    it('creates from mixed types', function () {
        expect(FloatProbability::fromBool(true)->value())->toBe(1.0)
            ->and(FloatProbability::fromDecimal('0.5')->value())->toBe(0.5)
            ->and(FloatProbability::fromFloat(0.2)->value())->toBe(0.2)
            ->and(FloatProbability::fromInt(0)->value())->toBe(0.0)
            ->and(FloatProbability::fromString('1.0')->value())->toBe(1.0);
    });

    it('tryFromMixed handles various inputs', function (mixed $input, bool $isValid) {
        $result = FloatProbability::tryFromMixed($input);
        if ($isValid) {
            expect($result)->toBeInstanceOf(FloatProbability::class);
        } else {
            expect($result)->toBeInstanceOf(Undefined::class);
        }
    })->with([
        'valid float' => [0.5, true],
        'out of range float' => [1.1, false],
        'valid int' => [1, true],
        'out of range int' => [2, false],
        'true bool' => [true, true],
        'false bool' => [false, true],
        'valid string' => ['0.5', true],
        'stringable' => [new class implements Stringable {
            public function __toString(): string
            {
                return '0.5';
            }
        }, true],
        'invalid string' => ['abc', false],
        'array' => [[], false],
        'object' => [new stdClass(), false],
    ]);

    it('has standard type methods', function () {
        $v = new FloatProbability(0.5);
        expect($v->isEmpty())->toBeFalse()
            ->and($v->isUndefined())->toBeFalse()
            ->and($v->isTypeOf(FloatProbability::class))->toBeTrue()
            ->and($v->isTypeOf(stdClass::class))->toBeFalse()
            ->and($v->jsonSerialize())->toBe(0.5)
            ->and($v->value())->toBe(0.5);
    });

    it('converts to other types', function () {
        $v = new FloatProbability(1.0);
        expect($v->toBool())->toBeTrue()
            ->and($v->toDecimal())->toBe('1.0')
            ->and($v->toFloat())->toBe(1.0)
            ->and($v->toInt())->toBe(1)
            ->and($v->toString())->toBe('1.0');
    });

    it('tryFrom* return Undefined on failure', function () {
        expect(FloatProbability::tryFromDecimal('1.1'))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbability::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbability::tryFromInt(2))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbability::tryFromString('1.1'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class FloatProbabilityTest extends FloatProbability
{
    public function __construct(float $value)
    {
        throw new Exception('test');
    }
}

describe('FloatProbability coverage', function () {
    it('kills tryFromMixed catch block', function () {
        expect(FloatProbabilityTest::tryFromMixed(0.5))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbabilityTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbabilityTest::tryFromDecimal('0.5'))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbabilityTest::tryFromFloat(0.5))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbabilityTest::tryFromInt(0))->toBeInstanceOf(Undefined::class)
            ->and(FloatProbabilityTest::tryFromString('0.5'))->toBeInstanceOf(Undefined::class);
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () {
        $v = new FloatProbability(0.5);
        expect($v->isTypeOf('NonExistentClass', FloatProbability::class, 'AnotherClass'))->toBeTrue();
    });
});
