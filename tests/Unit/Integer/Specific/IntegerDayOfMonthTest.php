<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\DayOfMonthIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerDayOfMonth;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerDayOfMonth::class);

describe('IntegerDayOfMonth', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 1-31', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->value())->toBe($value);
        })->with(range(1, 31));

        it('throws for values outside 1-31', function (int $invalidValue): void {
            expect(fn() => new IntegerDayOfMonth($invalidValue))
                ->toThrow(DayOfMonthIntegerTypeException::class, 'Expected value between 1-31');
        })->with([0, 32, -1, 100]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 1-31', function (int $value): void {
            $day = IntegerDayOfMonth::fromInt($value);
            expect($day->value())->toBe($value);
        })->with(range(1, 31));

        it('throws for values outside 1-31', function (int $invalidValue): void {
            expect(fn() => IntegerDayOfMonth::fromInt($invalidValue))
                ->toThrow(DayOfMonthIntegerTypeException::class, 'Expected value between 1-31');
        })->with([0, 32, -1, 100]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 1-31', function (string $value, int $expected): void {
            $day = IntegerDayOfMonth::fromString($value);
            expect($day->value())->toBe($expected);
        })->with([
            ['1', 1],
            ['31', 31],
            ['15', 15],
        ]);

        it('throws DayOfMonthIntegerTypeException for values outside 1-31', function (string $invalidValue): void {
            expect(fn() => IntegerDayOfMonth::fromString($invalidValue))
                ->toThrow(DayOfMonthIntegerTypeException::class, 'Expected value between 1-31');
        })->with(['0', '32', '-1', '100']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerDayOfMonth::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['5.5', StringTypeException::class],
            ['a', StringTypeException::class],
            ['', StringTypeException::class],
            ['3.0', StringTypeException::class],
            ['5.0', StringTypeException::class],
            ['01', StringTypeException::class],
            ['+1', StringTypeException::class],
            [' 1', StringTypeException::class],
            ['1 ', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true (1)', function (): void {
            $day = IntegerDayOfMonth::fromBool(true);
            expect($day->value())->toBe(1);
        });

        it('throws from false (0)', function (): void {
            expect(fn() => IntegerDayOfMonth::fromBool(false))
                ->toThrow(DayOfMonthIntegerTypeException::class, 'Expected value between 1-31, got "0"');
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 1-31', function (float $value, int $expected): void {
            $day = IntegerDayOfMonth::fromFloat($value);
            expect($day->value())->toBe($expected);
        })->with([
            [1.0, 1],
            [31.0, 31],
            [15.0, 15],
        ]);

        it('throws for float values outside 1-31', function (float $invalidValue): void {
            expect(fn() => IntegerDayOfMonth::fromFloat($invalidValue))
                ->toThrow(DayOfMonthIntegerTypeException::class, 'Expected value between 1-31');
        })->with([0.0, 32.0, -1.0, 100.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerDayOfMonth::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 1-31', function (string $value, int $expected): void {
            $day = IntegerDayOfMonth::fromDecimal($value);
            expect($day->value())->toBe($expected);
        })->with([
            ['1.0', 1],
            ['31.0', 31],
            ['15.0', 15],
        ]);

        it('throws for decimal values outside 1-31', function (string $invalidValue): void {
            expect(fn() => IntegerDayOfMonth::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['0.0', '32.0', '-1.0', '100.0', '1.1', '30.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerDayOfMonth::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerDayOfMonth for valid values 1-31', function (int $value): void {
            $result = IntegerDayOfMonth::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe($value);
        })->with(range(1, 31));

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([0, 32, -1, 100]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerDayOfMonth for valid integer strings 1-31', function (string $value, int $expected): void {
            $result = IntegerDayOfMonth::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['1', 1],
            ['31', 31],
        ]);

        it('returns Undefined for values outside 1-31', function (string $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['0', '32', '-1', '100']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            '5.5',
            'a',
            '',
            '3.0',
            '5.0',
            '01',
            '+1',
            ' 1',
            '1 ',
        ]);
    });

    describe('tryFromBool method', function (): void {
        it('returns IntegerDayOfMonth from true', function (): void {
            $result = IntegerDayOfMonth::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe(1);
        });

        it('returns Undefined from false', function (): void {
            $result = IntegerDayOfMonth::tryFromBool(false);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerDayOfMonth from float with exact integer value 1-31', function (float $value, int $expected): void {
            $result = IntegerDayOfMonth::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [1.0, 1],
            [31.0, 31],
            [15.0, 15],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([0.0, 32.0, 3.14, -1.0, 100.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerDayOfMonth from decimal string with exact integer value 1-31', function (string $value, int $expected): void {
            $result = IntegerDayOfMonth::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['1.0', 1],
            ['31.0', 31],
            ['15.0', 15],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['0.0', '32.0', '1.1', '30.1', '5', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerDayOfMonth for valid integer inputs 1-31', function (mixed $value, int $expected): void {
            $result = IntegerDayOfMonth::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [1, 1],
            [31, 31],
            ['15', 15],
            [true, 1],
            [4.0, 4],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerDayOfMonth::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [0],
            [32],
            ['0'],
            ['32'],
            [false],
            ['5.5'],
            ['a'],
            [''],
            ['7.0'],
            ['01'],
            ['+1'],
            [' 1'],
            ['1 '],
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
            $day = new IntegerDayOfMonth($value);
            expect($day->toInt())->toBe($value);
        })->with([1, 31, 15]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->toString())->toBe($expected)
                ->and((string) $day)->toBe($expected);
        })->with([
            [1, '1'],
            [31, '31'],
            [15, '15'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->toFloat())->toBe((float) $value)
                ->and($day->toFloat())->toBeFloat();
        })->with([1, 31, 15]);

        it('toBool returns true for all valid values (1-31)', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->toBool())->toBeTrue();
        })->with([1, 31, 15]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->toDecimal())->toBe($expected);
        })->with([
            [1, '1.0'],
            [31, '31.0'],
            [15, '15.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->jsonSerialize())->toBe($value)
                ->and($day->jsonSerialize())->toBeInt();
        })->with([1, 31, 15]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->isEmpty())->toBeFalse();
        })->with([1, 31, 15]);

        it('isUndefined always returns false', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->isUndefined())->toBeFalse();
        })->with([1, 31, 15]);

        it('isTypeOf returns true for matching class', function (): void {
            $day = IntegerDayOfMonth::fromInt(5);
            expect($day->isTypeOf(IntegerDayOfMonth::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $day = IntegerDayOfMonth::fromInt(5);
            expect($day->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $day = IntegerDayOfMonth::fromInt(5);
            expect($day->isTypeOf('NonExistentClass', IntegerDayOfMonth::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $day = new IntegerDayOfMonth($value);
            expect($day->value())->toBe($value);
        })->with([1, 31, 15]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerDayOfMonth::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerDayOfMonth::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([1, 31, 15]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerDayOfMonth::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerDayOfMonth::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['1', '31', '15']);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles multiple round-trips for all valid values', function (int $original): void {
            $result = IntegerDayOfMonth::fromString(
                IntegerDayOfMonth::fromInt(
                    IntegerDayOfMonth::fromString(
                        IntegerDayOfMonth::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([1, 31, 15]);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '15';
                }
            };

            $result = IntegerDayOfMonth::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerDayOfMonth::class)
                ->and($result->value())->toBe(15);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerDayOfMonth::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });
});
