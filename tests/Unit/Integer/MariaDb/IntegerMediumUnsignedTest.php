<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\UnsignedMediumIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerMediumUnsigned;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerMediumUnsigned::class);

describe('IntegerMediumUnsigned', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0..16777215', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->value())->toBe($value);
        })->with([0, 1, 1000, 16777215]);

        it('throws for values outside 0..16777215', function (int $invalidValue): void {
            expect(fn() => new IntegerMediumUnsigned($invalidValue))
                ->toThrow(UnsignedMediumIntegerTypeException::class, 'Expected unsigned medium integer in range 0..16777215');
        })->with([-1, 16777216, -100, 20000000]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0..16777215', function (int $value): void {
            $medium = IntegerMediumUnsigned::fromInt($value);
            expect($medium->value())->toBe($value);
        })->with([0, 1, 1000, 16777215]);

        it('throws for values outside 0..16777215', function (int $invalidValue): void {
            expect(fn() => IntegerMediumUnsigned::fromInt($invalidValue))
                ->toThrow(UnsignedMediumIntegerTypeException::class, 'Expected unsigned medium integer in range 0..16777215');
        })->with([-1, 16777216, -100, 20000000]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0..16777215', function (string $value, int $expected): void {
            $medium = IntegerMediumUnsigned::fromString($value);
            expect($medium->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            ['16777215', 16777215],
        ]);

        it('throws UnsignedMediumIntegerTypeException for values outside 0..16777215', function (string $invalidValue): void {
            expect(fn() => IntegerMediumUnsigned::fromString($invalidValue))
                ->toThrow(UnsignedMediumIntegerTypeException::class, 'Expected unsigned medium integer in range 0..16777215');
        })->with(['-1', '16777216', '20000000']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerMediumUnsigned::fromString($invalidValue))
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
            $medium = IntegerMediumUnsigned::fromBool(true);
            expect($medium->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $medium = IntegerMediumUnsigned::fromBool(false);
            expect($medium->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0..16777215', function (float $value, int $expected): void {
            $medium = IntegerMediumUnsigned::fromFloat($value);
            expect($medium->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [16777215.0, 16777215],
            [5.0, 5],
        ]);

        it('throws for float values outside 0..16777215', function (float $invalidValue): void {
            expect(fn() => IntegerMediumUnsigned::fromFloat($invalidValue))
                ->toThrow(UnsignedMediumIntegerTypeException::class, 'Expected unsigned medium integer in range 0..16777215');
        })->with([-1.0, 16777216.0, 20000000.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerMediumUnsigned::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0..16777215', function (string $value, int $expected): void {
            $medium = IntegerMediumUnsigned::fromDecimal($value);
            expect($medium->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['16777215.0', 16777215],
            ['5.0', 5],
        ]);

        it('throws for decimal values outside 0..16777215', function (string $invalidValue): void {
            expect(fn() => IntegerMediumUnsigned::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-1.0', '16777216.1', '16777216.0']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerMediumUnsigned::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerMediumUnsigned for valid values 0..16777215', function (int $value): void {
            $result = IntegerMediumUnsigned::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerMediumUnsigned::class)
                ->and($result->value())->toBe($value);
        })->with([0, 1, 16777215]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerMediumUnsigned::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 16777216]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerMediumUnsigned for valid integer strings 0..16777215', function (string $value, int $expected): void {
            $result = IntegerMediumUnsigned::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerMediumUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['16777215', 16777215],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerMediumUnsigned::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '16777216', 'abc', '']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerMediumUnsigned for valid integer inputs 0..16777215', function (mixed $value, int $expected): void {
            $result = IntegerMediumUnsigned::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerMediumUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [16777215, 16777215],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerMediumUnsigned::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [16777216],
            ['-1'],
            ['16777216'],
            ['12.3'],
            ['a'],
            [''],
            ['01'],
            [[]],
            [null],
            [new stdClass()],
        ]);
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('toInt returns integer value', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->toInt())->toBe($value);
        })->with([0, 1, 16777215]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->toString())->toBe($expected)
                ->and((string) $medium)->toBe($expected);
        })->with([
            [0, '0'],
            [16777215, '16777215'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->toFloat())->toBe((float) $value);
        })->with([0, 1, 16777215]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [16777215, '16777215.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->jsonSerialize())->toBe($value);
        })->with([0, 1, 16777215]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->isEmpty())->toBeFalse();
        })->with([0, 16777215]);

        it('isUndefined always returns false', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->isUndefined())->toBeFalse();
        })->with([0, 16777215]);

        it('isTypeOf returns true for matching class', function (): void {
            $medium = IntegerMediumUnsigned::fromInt(5);
            expect($medium->isTypeOf(IntegerMediumUnsigned::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $medium = IntegerMediumUnsigned::fromInt(5);
            expect($medium->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $medium = new IntegerMediumUnsigned($value);
            expect($medium->value())->toBe($value);
        })->with([0, 16777215]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerMediumUnsigned::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerMediumUnsigned::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 50, 16777215]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerMediumUnsigned::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerMediumUnsigned::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerMediumUnsigned::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFrom* methods return default on failure', function (): void {
            expect(IntegerMediumUnsigned::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromFloat(20000000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromString('20000000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromInt(20000000))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMediumUnsigned::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerMediumUnsignedTest extends IntegerMediumUnsigned
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

    describe('IntegerMediumUnsigned catch block coverage', function (): void {
        it('IntegerMediumUnsigned::tryFromBool catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFromFloat catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFromInt catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFromMixed catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMediumUnsigned::tryFromString catch block coverage', function (): void {
            expect(IntegerMediumUnsignedTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });
});
