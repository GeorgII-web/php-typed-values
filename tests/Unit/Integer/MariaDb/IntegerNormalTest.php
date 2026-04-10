<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\NormalIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerNormal;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerNormal::class);

describe('IntegerNormal', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values -2147483648..2147483647', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->value())->toBe($value);
        })->with([-2147483648, -1, 0, 1, 2147483647]);

        it('throws for values outside -2147483648..2147483647', function (int $invalidValue): void {
            expect(fn() => new IntegerNormal($invalidValue))
                ->toThrow(NormalIntegerTypeException::class, 'Expected normal integer in range -2147483648..2147483647');
        })->with([-2147483649, 2147483648]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values -2147483648..2147483647', function (int $value): void {
            $normal = IntegerNormal::fromInt($value);
            expect($normal->value())->toBe($value);
        })->with([-2147483648, -1, 0, 1, 2147483647]);

        it('throws for values outside -2147483648..2147483647', function (int $invalidValue): void {
            expect(fn() => IntegerNormal::fromInt($invalidValue))
                ->toThrow(NormalIntegerTypeException::class, 'Expected normal integer in range -2147483648..2147483647');
        })->with([-2147483649, 2147483648]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings -2147483648..2147483647', function (string $value, int $expected): void {
            $normal = IntegerNormal::fromString($value);
            expect($normal->value())->toBe($expected);
        })->with([
            ['-2147483648', -2147483648],
            ['0', 0],
            ['2147483647', 2147483647],
        ]);

        it('throws NormalIntegerTypeException for values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerNormal::fromString($invalidValue))
                ->toThrow(NormalIntegerTypeException::class, 'Expected normal integer in range -2147483648..2147483647');
        })->with(['-2147483649', '2147483648']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerNormal::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['12.3', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['01', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true', function (): void {
            $normal = IntegerNormal::fromBool(true);
            expect($normal->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $normal = IntegerNormal::fromBool(false);
            expect($normal->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value', function (float $value, int $expected): void {
            $normal = IntegerNormal::fromFloat($value);
            expect($normal->value())->toBe($expected);
        })->with([
            [-2147483648.0, -2147483648],
            [0.0, 0],
            [2147483647.0, 2147483647],
        ]);

        it('throws for float values outside range', function (float $invalidValue): void {
            expect(fn() => IntegerNormal::fromFloat($invalidValue))
                ->toThrow(NormalIntegerTypeException::class, 'Expected normal integer in range -2147483648..2147483647');
        })->with([-2147483649.0, 2147483648.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerNormal::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings', function (string $value, int $expected): void {
            $normal = IntegerNormal::fromDecimal($value);
            expect($normal->value())->toBe($expected);
        })->with([
            ['-2147483648.0', -2147483648],
            ['0.0', 0],
            ['2147483647.0', 2147483647],
        ]);

        it('throws for decimal values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerNormal::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-2147483648.1', '2147483647.1', '-2147483649.0', '2147483648.0']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerNormal for valid values', function (int $value): void {
            $result = IntegerNormal::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerNormal::class)
                ->and($result->value())->toBe($value);
        })->with([-2147483648, 0, 2147483647]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerNormal::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-2147483649, 2147483648]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerNormal for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerNormal::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerNormal::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-2147483648', -2147483648],
            ['2147483647', 2147483647],
        ]);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerNormal for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerNormal::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerNormal::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [2147483647, 2147483647],
            ['-5', -5],
            [true, 1],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerNormal::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-2147483649],
            [2147483648],
            ['abc'],
            [[]],
            [null],
            [new stdClass()],
        ]);
    });

    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('toInt returns integer value', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->toInt())->toBe($value);
        })->with([-2147483648, 0, 2147483647]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $normal = new IntegerNormal($value);
            expect($normal->toString())->toBe($expected)
                ->and((string) $normal)->toBe($expected);
        })->with([
            [-2147483648, '-2147483648'],
            [2147483647, '2147483647'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->toFloat())->toBe((float) $value);
        })->with([-2147483648, 0, 2147483647]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $normal = new IntegerNormal($value);
            expect($normal->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $normal = new IntegerNormal($value);
            expect($normal->toDecimal())->toBe($expected);
        })->with([
            [-2147483648, '-2147483648.0'],
            [2147483647, '2147483647.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->jsonSerialize())->toBe($value);
        })->with([-2147483648, 0, 2147483647]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->isEmpty())->toBeFalse();
        })->with([-2147483648, 2147483647]);

        it('isUndefined always returns false', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->isUndefined())->toBeFalse();
        })->with([-2147483648, 2147483647]);

        it('isTypeOf returns true for matching class', function (): void {
            $normal = IntegerNormal::fromInt(5);
            expect($normal->isTypeOf(IntegerNormal::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $normal = IntegerNormal::fromInt(5);
            expect($normal->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $normal = new IntegerNormal($value);
            expect($normal->value())->toBe($value);
        })->with([-2147483648, 2147483647]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerNormal::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNormal::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([-2147483648, 0, 2147483647]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerNormal::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerNormal::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerNormal::tryFromMixed([]))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFrom* methods return default on failure', function (): void {
            expect(IntegerNormal::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromFloat(3000000000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromString('3000000000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromInt(3000000000))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormal::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerNormalTest extends IntegerNormal
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

    describe('IntegerNormal catch block coverage', function (): void {
        it('IntegerNormal::tryFromBool catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFromFloat catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFromInt catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFromMixed catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormal::tryFromString catch block coverage', function (): void {
            expect(IntegerNormalTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerNormal::fromNull(null))
                ->toThrow(NormalIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerNormal::toNull())
                ->toThrow(NormalIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
