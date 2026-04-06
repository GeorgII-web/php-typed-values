<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\UnsignedNormalIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerNormalUnsigned;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerNormalUnsigned::class);

describe('IntegerNormalUnsigned', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0..4294967295', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->value())->toBe($value);
        })->with([0, 1, 1000, 4294967295]);

        it('throws for values outside 0..4294967295', function (int $invalidValue): void {
            expect(fn() => new IntegerNormalUnsigned($invalidValue))
                ->toThrow(UnsignedNormalIntegerTypeException::class, 'Expected unsigned normal integer in range 0..4294967295');
        })->with([-1, 4294967296]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0..4294967295', function (int $value): void {
            $normal = IntegerNormalUnsigned::fromInt($value);
            expect($normal->value())->toBe($value);
        })->with([0, 1, 1000, 4294967295]);

        it('throws for values outside 0..4294967295', function (int $invalidValue): void {
            expect(fn() => IntegerNormalUnsigned::fromInt($invalidValue))
                ->toThrow(UnsignedNormalIntegerTypeException::class, 'Expected unsigned normal integer in range 0..4294967295');
        })->with([-1, 4294967296]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0..4294967295', function (string $value, int $expected): void {
            $normal = IntegerNormalUnsigned::fromString($value);
            expect($normal->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            ['4294967295', 4294967295],
        ]);

        it('throws UnsignedNormalIntegerTypeException for values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerNormalUnsigned::fromString($invalidValue))
                ->toThrow(UnsignedNormalIntegerTypeException::class, 'Expected unsigned normal integer in range 0..4294967295');
        })->with(['-1', '4294967296']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerNormalUnsigned::fromString($invalidValue))
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
            $normal = IntegerNormalUnsigned::fromBool(true);
            expect($normal->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $normal = IntegerNormalUnsigned::fromBool(false);
            expect($normal->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value', function (float $value, int $expected): void {
            $normal = IntegerNormalUnsigned::fromFloat($value);
            expect($normal->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [4294967295.0, 4294967295],
            [5.0, 5],
        ]);

        it('throws for float values outside range', function (float $invalidValue): void {
            expect(fn() => IntegerNormalUnsigned::fromFloat($invalidValue))
                ->toThrow(UnsignedNormalIntegerTypeException::class, 'Expected unsigned normal integer in range 0..4294967295');
        })->with([-1.0, 4294967296.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerNormalUnsigned::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings', function (string $value, int $expected): void {
            $normal = IntegerNormalUnsigned::fromDecimal($value);
            expect($normal->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['4294967295.0', 4294967295],
            ['5.0', 5],
        ]);

        it('throws for decimal values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerNormalUnsigned::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-1.0', '4294967296.1', '4294967296.0']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerNormalUnsigned for valid values', function (int $value): void {
            $result = IntegerNormalUnsigned::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerNormalUnsigned::class)
                ->and($result->value())->toBe($value);
        })->with([0, 1, 4294967295]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerNormalUnsigned::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 4294967296]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerNormalUnsigned for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerNormalUnsigned::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerNormalUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['4294967295', 4294967295],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerNormalUnsigned::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '4294967296', 'abc', '']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerNormalUnsigned for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerNormalUnsigned::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerNormalUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [4294967295, 4294967295],
            ['0', 0],
            [true, 1],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerNormalUnsigned::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [4294967296],
            ['-1'],
            ['4294967296'],
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
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->toInt())->toBe($value);
        })->with([0, 1, 4294967295]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->toString())->toBe($expected)
                ->and((string) $normal)->toBe($expected);
        })->with([
            [0, '0'],
            [4294967295, '4294967295'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->toFloat())->toBe((float) $value);
        })->with([0, 1, 4294967295]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [4294967295, '4294967295.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->jsonSerialize())->toBe($value);
        })->with([0, 1, 4294967295]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->isEmpty())->toBeFalse();
        })->with([0, 4294967295]);

        it('isUndefined always returns false', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->isUndefined())->toBeFalse();
        })->with([0, 4294967295]);

        it('isTypeOf returns true for matching class', function (): void {
            $normal = IntegerNormalUnsigned::fromInt(5);
            expect($normal->isTypeOf(IntegerNormalUnsigned::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $normal = IntegerNormalUnsigned::fromInt(5);
            expect($normal->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns false for no arguments', function (): void {
            $normal = IntegerNormalUnsigned::fromInt(5);
            expect($normal->isTypeOf())->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $normal = new IntegerNormalUnsigned($value);
            expect($normal->value())->toBe($value);
        })->with([0, 4294967295]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerNormalUnsigned::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNormalUnsigned::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 50, 4294967295]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerNormalUnsigned::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerNormalUnsigned::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerNormalUnsigned::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFrom* methods return default on failure', function (): void {
            expect(IntegerNormalUnsigned::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromFloat(5000000000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromString('5000000000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromInt(5000000000))->toBeInstanceOf(Undefined::class)
                ->and(IntegerNormalUnsigned::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerNormalUnsignedTest extends IntegerNormalUnsigned
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

    describe('IntegerNormalUnsigned catch block coverage', function (): void {
        it('IntegerNormalUnsigned::tryFromBool catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFromFloat catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFromInt catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFromMixed catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerNormalUnsigned::tryFromString catch block coverage', function (): void {
            expect(IntegerNormalUnsignedTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });
});
