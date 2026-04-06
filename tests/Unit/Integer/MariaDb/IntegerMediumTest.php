<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\MediumIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerMedium;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerMedium::class);

describe('IntegerMedium', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values -8388608..8388607', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->value())->toBe($value);
        })->with([-8388608, -1, 0, 1, 8388607]);

        it('throws for values outside -8388608..8388607', function (int $invalidValue): void {
            expect(fn() => new IntegerMedium($invalidValue))
                ->toThrow(MediumIntegerTypeException::class, 'Expected medium integer in range -8388608..8388607');
        })->with([-8388609, 8388608, -10000000, 10000000]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values -8388608..8388607', function (int $value): void {
            $medium = IntegerMedium::fromInt($value);
            expect($medium->value())->toBe($value);
        })->with([-8388608, -1, 0, 1, 8388607]);

        it('throws for values outside -8388608..8388607', function (int $invalidValue): void {
            expect(fn() => IntegerMedium::fromInt($invalidValue))
                ->toThrow(MediumIntegerTypeException::class, 'Expected medium integer in range -8388608..8388607');
        })->with([-8388609, 8388608, -10000000, 10000000]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings -8388608..8388607', function (string $value, int $expected): void {
            $medium = IntegerMedium::fromString($value);
            expect($medium->value())->toBe($expected);
        })->with([
            ['-8388608', -8388608],
            ['0', 0],
            ['8388607', 8388607],
            ['-5', -5],
        ]);

        it('throws MediumIntegerTypeException for values outside -8388608..8388607', function (string $invalidValue): void {
            expect(fn() => IntegerMedium::fromString($invalidValue))
                ->toThrow(MediumIntegerTypeException::class, 'Expected medium integer in range -8388608..8388607');
        })->with(['-8388609', '8388608', '-10000000', '10000000']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerMedium::fromString($invalidValue))
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
            $medium = IntegerMedium::fromBool(true);
            expect($medium->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $medium = IntegerMedium::fromBool(false);
            expect($medium->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value -8388608..8388607', function (float $value, int $expected): void {
            $medium = IntegerMedium::fromFloat($value);
            expect($medium->value())->toBe($expected);
        })->with([
            [-8388608.0, -8388608],
            [0.0, 0],
            [8388607.0, 8388607],
            [5.0, 5],
        ]);

        it('throws for float values outside -8388608..8388607', function (float $invalidValue): void {
            expect(fn() => IntegerMedium::fromFloat($invalidValue))
                ->toThrow(MediumIntegerTypeException::class, 'Expected medium integer in range -8388608..8388607');
        })->with([-8388609.0, 8388608.0, -10000000.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerMedium::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings -8388608..8388607', function (string $value, int $expected): void {
            $medium = IntegerMedium::fromDecimal($value);
            expect($medium->value())->toBe($expected);
        })->with([
            ['-8388608.0', -8388608],
            ['0.0', 0],
            ['8388607.0', 8388607],
            ['5.0', 5],
        ]);

        it('throws for decimal values outside -8388608..8388607', function (string $invalidValue): void {
            expect(fn() => IntegerMedium::fromDecimal($invalidValue))
                ->toThrow(Exception::class);
        })->with(['-8388608.1', '8388607.1', '-8388609.0', '8388608.0']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerMedium::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerMedium for valid values -8388608..8388607', function (int $value): void {
            $result = IntegerMedium::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerMedium::class)
                ->and($result->value())->toBe($value);
        })->with([-8388608, -1, 0, 1, 8388607]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerMedium::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-8388609, 8388608, -10000000, 10000000]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerMedium for valid integer strings -8388608..8388607', function (string $value, int $expected): void {
            $result = IntegerMedium::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerMedium::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-8388608', -8388608],
            ['0', 0],
            ['8388607', 8388607],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerMedium::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-8388609', '8388608', '12.3', 'abc', '']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerMedium for valid integer inputs -8388608..8388607', function (mixed $value, int $expected): void {
            $result = IntegerMedium::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerMedium::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-1, -1],
            [8388607, 8388607],
            ['-5', -5],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerMedium::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-8388609],
            [8388608],
            ['-8388609'],
            ['8388608'],
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
            $medium = new IntegerMedium($value);
            expect($medium->toInt())->toBe($value);
        })->with([-8388608, -1, 0, 1, 8388607]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $medium = new IntegerMedium($value);
            expect($medium->toString())->toBe($expected)
                ->and((string) $medium)->toBe($expected);
        })->with([
            [-8388608, '-8388608'],
            [0, '0'],
            [8388607, '8388607'],
            [-5, '-5'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->toFloat())->toBe((float) $value);
        })->with([-8388608, -1, 0, 1, 8388607]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $medium = new IntegerMedium($value);
            expect($medium->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $medium = new IntegerMedium($value);
            expect($medium->toDecimal())->toBe($expected);
        })->with([
            [-8388608, '-8388608.0'],
            [0, '0.0'],
            [8388607, '8388607.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->jsonSerialize())->toBe($value);
        })->with([-8388608, -1, 0, 1, 8388607]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->isEmpty())->toBeFalse();
        })->with([-8388608, 0, 8388607]);

        it('isUndefined always returns false', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->isUndefined())->toBeFalse();
        })->with([-8388608, 0, 8388607]);

        it('isTypeOf returns true for matching class', function (): void {
            $medium = IntegerMedium::fromInt(5);
            expect($medium->isTypeOf(IntegerMedium::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $medium = IntegerMedium::fromInt(5);
            expect($medium->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns false for no arguments', function (): void {
            $medium = IntegerMedium::fromInt(5);
            expect($medium->isTypeOf())->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $medium = new IntegerMedium($value);
            expect($medium->value())->toBe($value);
        })->with([-8388608, 0, 8388607]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerMedium::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerMedium::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([-8388608, -50, 0, 50, 8388607]);
    });

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerMedium::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerMedium::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed returns default on failure', function (): void {
            expect(IntegerMedium::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFrom* methods return default on failure', function (): void {
            expect(IntegerMedium::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromFloat(10000000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromString('10000000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromInt(10000000))->toBeInstanceOf(Undefined::class)
                ->and(IntegerMedium::tryFromDecimal('1.5'))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerMediumTest extends IntegerMedium
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

    describe('IntegerMedium catch block coverage', function (): void {
        it('IntegerMedium::tryFromBool catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFromFloat catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFromInt catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFromMixed catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerMedium::tryFromString catch block coverage', function (): void {
            expect(IntegerMediumTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });
});
