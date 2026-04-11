<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\UnsignedTinyIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerTinyUnsigned;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerTinyUnsigned::class);

describe('IntegerTinyUnsigned', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0..255', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->value())->toBe($value);
        })->with([0, 1, 127, 128, 255]);

        it('throws for values outside 0..255', function (int $invalidValue): void {
            expect(fn() => new IntegerTinyUnsigned($invalidValue))
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Expected unsigned tiny integer in range 0..255');
        })->with([-1, 256, -100, 300]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0..255', function (int $value): void {
            $tiny = IntegerTinyUnsigned::fromInt($value);
            expect($tiny->value())->toBe($value);
        })->with([0, 1, 127, 128, 255]);

        it('throws for values outside 0..255', function (int $invalidValue): void {
            expect(fn() => IntegerTinyUnsigned::fromInt($invalidValue))
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Expected unsigned tiny integer in range 0..255');
        })->with([-1, 256, -100, 300]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0..255', function (string $value, int $expected): void {
            $tiny = IntegerTinyUnsigned::fromString($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            ['127', 127],
            ['255', 255],
        ]);

        it('throws UnsignedTinyIntegerTypeException for values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerTinyUnsigned::fromString($invalidValue))
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Expected unsigned tiny integer in range 0..255');
        })->with(['-1', '256', '300']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerTinyUnsigned::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['12.3', StringTypeException::class],
            ['5.5', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['01', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true', function (): void {
            $tiny = IntegerTinyUnsigned::fromBool(true);
            expect($tiny->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $tiny = IntegerTinyUnsigned::fromBool(false);
            expect($tiny->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0..255', function (float $value, int $expected): void {
            $tiny = IntegerTinyUnsigned::fromFloat($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [127.0, 127],
            [255.0, 255],
        ]);

        it('throws for float values outside 0..255', function (float $invalidValue): void {
            expect(fn() => IntegerTinyUnsigned::fromFloat($invalidValue))
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Expected unsigned tiny integer in range 0..255');
        })->with([-1.0, 256.0, 300.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerTinyUnsigned::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0..255', function (string $value, int $expected): void {
            $tiny = IntegerTinyUnsigned::fromDecimal($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['127.0', 127],
            ['255.0', 255],
        ]);

        it('throws for decimal values outside range', function (string $invalidValue): void {
            expect(fn() => IntegerTinyUnsigned::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-1.0', '256.0', '255.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerTinyUnsigned::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerTinyUnsigned for valid values', function (int $value): void {
            $result = IntegerTinyUnsigned::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerTinyUnsigned::class)
                ->and($result->value())->toBe($value);
        })->with([0, 127, 255]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerTinyUnsigned::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 256]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerTinyUnsigned for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerTinyUnsigned::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerTinyUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['255', 255],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerTinyUnsigned::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '256', 'abc', '']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerTinyUnsigned for valid inputs', function (mixed $value, int $expected): void {
            $result = IntegerTinyUnsigned::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerTinyUnsigned::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [255, 255],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerTinyUnsigned::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [256],
            ['-1'],
            ['256'],
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
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->toInt())->toBe($value);
        })->with([0, 127, 255]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->toString())->toBe($expected)
                ->and((string) $tiny)->toBe($expected);
        })->with([
            [0, '0'],
            [255, '255'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->toFloat())->toBe((float) $value);
        })->with([0, 127, 255]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [255, '255.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->jsonSerialize())->toBe($value);
        })->with([0, 255]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->isEmpty())->toBeFalse();
        })->with([0, 255]);

        it('isUndefined always returns false', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->isUndefined())->toBeFalse();
        })->with([0, 255]);

        it('isTypeOf returns true for matching class', function (): void {
            $tiny = IntegerTinyUnsigned::fromInt(5);
            expect($tiny->isTypeOf(IntegerTinyUnsigned::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $tiny = IntegerTinyUnsigned::fromInt(5);
            expect($tiny->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns false for no arguments', function (): void {
            $tiny = IntegerTinyUnsigned::fromInt(5);
            expect($tiny->isTypeOf())->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $tiny = new IntegerTinyUnsigned($value);
            expect($tiny->value())->toBe($value);
        })->with([0, 255]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerTinyUnsigned::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerTinyUnsigned::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 50, 255]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerTinyUnsigned::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerTinyUnsigned::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerTinyUnsigned::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFrom* methods return default on failure', function (): void {
            expect(IntegerTinyUnsigned::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromFloat(256.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromString('256'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromInt(256))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTinyUnsigned::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerTinyUnsignedTest extends IntegerTinyUnsigned
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

    describe('IntegerTinyUnsigned catch block coverage', function (): void {
        it('IntegerTinyUnsigned::tryFromBool catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFromFloat catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFromInt catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFromMixed catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTinyUnsigned::tryFromString catch block coverage', function (): void {
            expect(IntegerTinyUnsignedTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerTinyUnsigned::fromNull(null))
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerTinyUnsigned::toNull())
                ->toThrow(UnsignedTinyIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
