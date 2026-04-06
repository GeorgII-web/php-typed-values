<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

use Exception;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\UnsignedBigIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerBigUnsigned;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerBigUnsigned::class);

describe('IntegerBigUnsigned', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0..PHP_INT_MAX', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->value())->toBe($value);
        })->with([0, 1, 1000, PHP_INT_MAX]);

        it('throws for negative values', function (int $invalidValue): void {
            expect(fn() => new IntegerBigUnsigned($invalidValue))
                ->toThrow(UnsignedBigIntegerTypeException::class, 'Expected unsigned big integer');
        })->with([-1, -100, PHP_INT_MIN]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0..PHP_INT_MAX', function (int $value): void {
            $big = IntegerBigUnsigned::fromInt($value);
            expect($big->value())->toBe($value);
        })->with([0, 1, 1000, PHP_INT_MAX]);

        it('throws for negative values', function (int $invalidValue): void {
            expect(fn() => IntegerBigUnsigned::fromInt($invalidValue))
                ->toThrow(UnsignedBigIntegerTypeException::class, 'Expected unsigned big integer');
        })->with([-1, -100, PHP_INT_MIN]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0..PHP_INT_MAX', function (string $value, int $expected): void {
            $big = IntegerBigUnsigned::fromString($value);
            expect($big->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            [(string) PHP_INT_MAX, PHP_INT_MAX],
        ]);

        it('throws for invalid strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerBigUnsigned::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['-1', UnsignedBigIntegerTypeException::class],
            ['12.3', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['01', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true', function (): void {
            $big = IntegerBigUnsigned::fromBool(true);
            expect($big->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $big = IntegerBigUnsigned::fromBool(false);
            expect($big->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value', function (float $value, int $expected): void {
            $big = IntegerBigUnsigned::fromFloat($value);
            expect($big->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [9007199254740991.0, 9007199254740991],
            [5.0, 5],
        ]);

        it('throws for negative or non-integer floats', function (float $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerBigUnsigned::fromFloat($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            [-1.0, UnsignedBigIntegerTypeException::class],
            [3.14, FloatTypeException::class],
        ]);
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings', function (string $value, int $expected): void {
            $big = IntegerBigUnsigned::fromDecimal($value);
            expect($big->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['9007199254740991.0', 9007199254740991],
            ['5.0', 5],
        ]);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerBigUnsigned::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-1.0', '12.3', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerBigUnsigned for valid values', function (int $value): void {
            $result = IntegerBigUnsigned::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerBigUnsigned::class)
                ->and($result->value())->toBe($value);
        })->with([0, 1, PHP_INT_MAX]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerBigUnsigned::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, PHP_INT_MIN]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerBigUnsigned for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerBigUnsigned::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerBigUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            [(string) PHP_INT_MAX, PHP_INT_MAX],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerBigUnsigned::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '12.3', 'abc', '']);
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('toInt returns integer value', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->toInt())->toBe($value);
        })->with([0, 1, PHP_INT_MAX]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->toString())->toBe($expected)
                ->and((string) $big)->toBe($expected);
        })->with([
            [0, '0'],
            [PHP_INT_MAX, (string) PHP_INT_MAX],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->toFloat())->toBe((float) $value);
        })->with([0, 1, 9007199254740991]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [9007199254740991, '9007199254740991.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->jsonSerialize())->toBe($value);
        })->with([0, 1, PHP_INT_MAX]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->isEmpty())->toBeFalse();
        })->with([0, PHP_INT_MAX]);

        it('isUndefined always returns false', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->isUndefined())->toBeFalse();
        })->with([0, PHP_INT_MAX]);

        it('isTypeOf returns true for matching class', function (): void {
            $big = IntegerBigUnsigned::fromInt(5);
            expect($big->isTypeOf(IntegerBigUnsigned::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $big = IntegerBigUnsigned::fromInt(5);
            expect($big->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $big = new IntegerBigUnsigned($value);
            expect($big->value())->toBe($value);
        })->with([0, PHP_INT_MAX]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerBigUnsigned::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerBigUnsigned::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 50, PHP_INT_MAX]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerBigUnsigned::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerBigUnsigned::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerBigUnsigned::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerBigUnsigned::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(IntegerBigUnsigned::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerBigUnsignedTest extends IntegerBigUnsigned
    {
        public static function fromBool(bool $value): static
        {
            throw new UnsignedBigIntegerTypeException('test');
        }

        public static function fromDecimal(string $value): static
        {
            throw new UnsignedBigIntegerTypeException('test');
        }

        public static function fromFloat(float $value): static
        {
            throw new UnsignedBigIntegerTypeException('test');
        }

        public static function fromInt(int $value): static
        {
            throw new UnsignedBigIntegerTypeException('test');
        }

        public static function fromString(string $value): static
        {
            throw new UnsignedBigIntegerTypeException('test');
        }
    }

    describe('IntegerBigUnsigned catch block coverage', function (): void {
        it('IntegerBigUnsigned::tryFromBool catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBigUnsigned::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBigUnsigned::tryFromFloat catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBigUnsigned::tryFromInt catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBigUnsigned::tryFromMixed catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBigUnsigned::tryFromString catch block coverage', function (): void {
            expect(IntegerBigUnsignedTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });
});
