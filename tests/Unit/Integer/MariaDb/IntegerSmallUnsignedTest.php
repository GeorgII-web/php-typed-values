<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\UnsignedSmallIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerSmallUnsigned;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerSmallUnsigned::class);

describe('IntegerSmallUnsigned', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0..65535', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->value())->toBe($value);
        })->with([0, 1, 1000, 65535]);

        it('throws for values outside 0..65535', function (int $invalidValue): void {
            expect(fn() => new IntegerSmallUnsigned($invalidValue))
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Expected unsigned small integer in range 0..65535');
        })->with([-1, 65536]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0..65535', function (int $value): void {
            $small = IntegerSmallUnsigned::fromInt($value);
            expect($small->value())->toBe($value);
        })->with([0, 1, 1000, 65535]);

        it('throws for values outside 0..65535', function (int $invalidValue): void {
            expect(fn() => IntegerSmallUnsigned::fromInt($invalidValue))
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Expected unsigned small integer in range 0..65535');
        })->with([-1, 65536]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0..65535', function (string $value, int $expected): void {
            $small = IntegerSmallUnsigned::fromString($value);
            expect($small->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            ['1000', 1000],
            ['65535', 65535],
        ]);

        it('throws UnsignedSmallIntegerTypeException for values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerSmallUnsigned::fromString($invalidValue))
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Expected unsigned small integer in range 0..65535');
        })->with(['-1', '65536', '100000']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerSmallUnsigned::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['12.3', StringTypeException::class],
            ['5.5', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['3.0', StringTypeException::class],
            ['01', StringTypeException::class],
            ['+1', StringTypeException::class],
            [' 1', StringTypeException::class],
            ['1 ', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true', function (): void {
            $small = IntegerSmallUnsigned::fromBool(true);
            expect($small->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $small = IntegerSmallUnsigned::fromBool(false);
            expect($small->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0..65535', function (float $value, int $expected): void {
            $small = IntegerSmallUnsigned::fromFloat($value);
            expect($small->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [65535.0, 65535],
            [100.0, 100],
        ]);

        it('throws for float values outside 0..65535', function (float $invalidValue): void {
            expect(fn() => IntegerSmallUnsigned::fromFloat($invalidValue))
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Expected unsigned small integer in range 0..65535');
        })->with([-1.0, 65536.0, 100000.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerSmallUnsigned::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0..65535', function (string $value, int $expected): void {
            $small = IntegerSmallUnsigned::fromDecimal($value);
            expect($small->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['65535.0', 65535],
            ['123.0', 123],
        ]);

        it('throws for decimal values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerSmallUnsigned::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-1.0', '65536.0', '65535.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerSmallUnsigned::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerSmallUnsigned for valid values', function (int $value): void {
            $result = IntegerSmallUnsigned::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerSmallUnsigned::class)
                ->and($result->value())->toBe($value);
        })->with([0, 100, 65535]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerSmallUnsigned::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 65536]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerSmallUnsigned for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerSmallUnsigned::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerSmallUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['65535', 65535],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerSmallUnsigned::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '65536', 'abc', '']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerSmallUnsigned for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerSmallUnsigned::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerSmallUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [65535, 65535],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerSmallUnsigned::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [65536],
            ['-1'],
            ['65536'],
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
            $small = new IntegerSmallUnsigned($value);
            expect($small->toInt())->toBe($value);
        })->with([0, 100, 65535]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->toString())->toBe($expected)
                ->and((string) $small)->toBe($expected);
        })->with([
            [0, '0'],
            [65535, '65535'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->toFloat())->toBe((float) $value);
        })->with([0, 100, 65535]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [65535, '65535.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->jsonSerialize())->toBe($value);
        })->with([0, 65535]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->isEmpty())->toBeFalse();
        })->with([0, 65535]);

        it('isUndefined always returns false', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->isUndefined())->toBeFalse();
        })->with([0, 65535]);

        it('isTypeOf returns true for matching class', function (): void {
            $small = IntegerSmallUnsigned::fromInt(5);
            expect($small->isTypeOf(IntegerSmallUnsigned::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $small = IntegerSmallUnsigned::fromInt(5);
            expect($small->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns false for no arguments', function (): void {
            $small = IntegerSmallUnsigned::fromInt(5);
            expect($small->isTypeOf())->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $small = new IntegerSmallUnsigned($value);
            expect($small->value())->toBe($value);
        })->with([0, 65535]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerSmallUnsigned::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerSmallUnsigned::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 50, 65535]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerSmallUnsigned::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerSmallUnsigned::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerSmallUnsigned::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFrom* methods return default on failure', function (): void {
            expect(IntegerSmallUnsigned::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromFloat(70000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromString('70000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromInt(70000))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmallUnsigned::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerSmallUnsignedTest extends IntegerSmallUnsigned
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

    describe('IntegerSmallUnsigned catch block coverage', function (): void {
        it('IntegerSmallUnsigned::tryFromBool catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFromFloat catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFromInt catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFromMixed catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmallUnsigned::tryFromString catch block coverage', function (): void {
            expect(IntegerSmallUnsignedTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerSmallUnsigned::fromNull(null))
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerSmallUnsigned::toNull())
                ->toThrow(UnsignedSmallIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
