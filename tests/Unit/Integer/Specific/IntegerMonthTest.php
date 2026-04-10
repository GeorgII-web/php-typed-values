<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\MonthIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerMonth;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerMonth::class);

describe('IntegerMonth', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 1-12', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->value())->toBe($value);
        })->with([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);

        it('throws for values outside 1-12', function (int $invalidValue): void {
            expect(fn() => new IntegerMonth($invalidValue))
                ->toThrow(MonthIntegerTypeException::class, 'Expected value between 1-12');
        })->with([0, 13, -1, 20]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 1-12', function (int $value): void {
            $month = IntegerMonth::fromInt($value);
            expect($month->value())->toBe($value);
        })->with([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);

        it('throws for values outside 1-12', function (int $invalidValue): void {
            expect(fn() => IntegerMonth::fromInt($invalidValue))
                ->toThrow(MonthIntegerTypeException::class, 'Expected value between 1-12');
        })->with([0, 13, -1, 20]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 1-12', function (string $value, int $expected): void {
            $month = IntegerMonth::fromString($value);
            expect($month->value())->toBe($expected);
        })->with([
            ['1', 1],
            ['12', 12],
        ]);

        it('throws MonthIntegerTypeException for values outside 1-12', function (string $invalidValue): void {
            expect(fn() => IntegerMonth::fromString($invalidValue))
                ->toThrow(MonthIntegerTypeException::class, 'Expected value between 1-12');
        })->with(['0', '13', '-1', '20']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerMonth::fromString($invalidValue))
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
        it('creates instance from true', function (): void {
            $month = IntegerMonth::fromBool(true);
            expect($month->value())->toBe(1);
        });

        it('throws from false', function (): void {
            expect(fn() => IntegerMonth::fromBool(false))
                ->toThrow(MonthIntegerTypeException::class, 'Expected value between 1-12, got "0"');
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 1-12', function (float $value, int $expected): void {
            $month = IntegerMonth::fromFloat($value);
            expect($month->value())->toBe($expected);
        })->with([
            [1.0, 1],
            [12.0, 12],
            [3.0, 3],
        ]);

        it('throws for float values outside 1-12', function (float $invalidValue): void {
            expect(fn() => IntegerMonth::fromFloat($invalidValue))
                ->toThrow(MonthIntegerTypeException::class, 'Expected value between 1-12');
        })->with([0.0, 13.0, -1.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerMonth::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 1-12', function (string $value, int $expected): void {
            $month = IntegerMonth::fromDecimal($value);
            expect($month->value())->toBe($expected);
        })->with([
            ['1.0', 1],
            ['12.0', 12],
            ['3.0', 3],
        ]);

        it('throws for decimal values outside 1-12', function (string $invalidValue): void {
            expect(fn() => IntegerMonth::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['0.0', '13.0', '-1.0', '1.1', '12.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerMonth::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    describe('fromLabel factory', function (): void {
        it('creates instance from valid month labels', function (string $label, int $expectedValue): void {
            $month = IntegerMonth::fromLabel($label);
            expect($month->value())->toBe($expectedValue);
        })->with([
            ['January', 1],
            ['February', 2],
            ['March', 3],
            ['April', 4],
            ['May', 5],
            ['June', 6],
            ['July', 7],
            ['August', 8],
            ['September', 9],
            ['October', 10],
            ['November', 11],
            ['December', 12],
        ]);

        it('throws for invalid month labels', function (string $invalidLabel): void {
            expect(fn() => IntegerMonth::fromLabel($invalidLabel))
                ->toThrow(MonthIntegerTypeException::class, 'Expected month label');
        })->with([
            'january',
            'Jan',
            '',
            'InvalidMonth',
            'January ',
        ]);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerMonth for valid values 1-12', function (int $value): void {
            $result = IntegerMonth::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe($value);
        })->with([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerMonth::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([0, 13, -1, 20]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerMonth for valid integer strings 1-12', function (string $value, int $expected): void {
            $result = IntegerMonth::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['1', 1],
            ['12', 12],
        ]);

        it('returns Undefined for values outside 1-12', function (string $invalidValue): void {
            $result = IntegerMonth::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['0', '13', '-1', '20']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerMonth::tryFromString($invalidValue);
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
        it('returns IntegerMonth from true', function (): void {
            $result = IntegerMonth::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe(1);
        });

        it('returns Undefined from false', function (): void {
            $result = IntegerMonth::tryFromBool(false);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerMonth from float with exact integer value 1-12', function (float $value, int $expected): void {
            $result = IntegerMonth::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [1.0, 1],
            [12.0, 12],
            [3.0, 3],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerMonth::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([0.0, 13.0, 3.14, -1.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerMonth from decimal string with exact integer value 1-12', function (string $value, int $expected): void {
            $result = IntegerMonth::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['1.0', 1],
            ['12.0', 12],
            ['3.0', 3],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerMonth::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['0.0', '13.0', '-1.0', '1.1', '12.1', '5', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerMonth for valid integer inputs 1-12', function (mixed $value, int $expected): void {
            $result = IntegerMonth::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [1, 1],
            [12, 12],
            ['3', 3],
            [true, 1],
            [4.0, 4],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerMonth::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [0],
            [13],
            [false],
            ['0'],
            ['13'],
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
            $month = new IntegerMonth($value);
            expect($month->toInt())->toBe($value);
        })->with([1, 12, 6]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $month = new IntegerMonth($value);
            expect($month->toString())->toBe($expected)
                ->and((string) $month)->toBe($expected);
        })->with([
            [1, '1'],
            [12, '12'],
            [3, '3'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->toFloat())->toBe((float) $value)
                ->and($month->toFloat())->toBeFloat();
        })->with([1, 12, 6]);

        it('toBool returns true for all values', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->toBool())->toBeTrue();
        })->with([1, 12, 6]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $month = new IntegerMonth($value);
            expect($month->toDecimal())->toBe($expected);
        })->with([
            [1, '1.0'],
            [12, '12.0'],
            [3, '3.0'],
        ]);

        it('toLabel returns correct month name', function (int $value, string $expectedLabel): void {
            $month = new IntegerMonth($value);
            expect($month->toLabel())->toBe($expectedLabel);
        })->with([
            [1, 'January'],
            [2, 'February'],
            [3, 'March'],
            [4, 'April'],
            [5, 'May'],
            [6, 'June'],
            [7, 'July'],
            [8, 'August'],
            [9, 'September'],
            [10, 'October'],
            [11, 'November'],
            [12, 'December'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->jsonSerialize())->toBe($value)
                ->and($month->jsonSerialize())->toBeInt();
        })->with([1, 12, 6]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->isEmpty())->toBeFalse();
        })->with([1, 12, 6]);

        it('isUndefined always returns false', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->isUndefined())->toBeFalse();
        })->with([1, 12, 6]);

        it('isTypeOf returns true for matching class', function (): void {
            $month = IntegerMonth::fromInt(5);
            expect($month->isTypeOf(IntegerMonth::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $month = IntegerMonth::fromInt(5);
            expect($month->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $month = IntegerMonth::fromInt(5);
            expect($month->isTypeOf('NonExistentClass', IntegerMonth::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $month = new IntegerMonth($value);
            expect($month->value())->toBe($value);
        })->with([1, 12, 6]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerMonth::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerMonth::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([1, 12, 6]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerMonth::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerMonth::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['1', '12', '6']);

        it('preserves value through label → int → label conversion', function (string $label, int $expectedValue): void {
            $month = IntegerMonth::fromLabel($label);
            $value = $month->value();
            $resultLabel = IntegerMonth::fromInt($value)->toLabel();
            expect($resultLabel)->toBe($label)
                ->and($value)->toBe($expectedValue);
        })->with([
            ['January', 1],
            ['February', 2],
            ['March', 3],
            ['April', 4],
            ['May', 5],
            ['June', 6],
            ['July', 7],
            ['August', 8],
            ['September', 9],
            ['October', 10],
            ['November', 11],
            ['December', 12],
        ]);

        it('preserves value through int → label → int conversion', function (int $value, string $expectedLabel): void {
            $month = IntegerMonth::fromInt($value);
            $label = $month->toLabel();
            $resultValue = IntegerMonth::fromLabel($label)->value();
            expect($label)->toBe($expectedLabel)
                ->and($resultValue)->toBe($value);
        })->with([
            [1, 'January'],
            [2, 'February'],
            [3, 'March'],
            [4, 'April'],
            [5, 'May'],
            [6, 'June'],
            [7, 'July'],
            [8, 'August'],
            [9, 'September'],
            [10, 'October'],
            [11, 'November'],
            [12, 'December'],
        ]);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles multiple round-trips for all valid values', function (int $original): void {
            $result = IntegerMonth::fromString(
                IntegerMonth::fromInt(
                    IntegerMonth::fromString(
                        IntegerMonth::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([1, 12, 6]);

        it('handles multiple round-trips for all labels', function (string $label): void {
            $result = IntegerMonth::fromInt(
                IntegerMonth::fromLabel(
                    IntegerMonth::fromInt(
                        IntegerMonth::fromLabel($label)->value()
                    )->toLabel()
                )->value()
            )->toLabel();

            expect($result)->toBe($label);
        })->with(['January', 'December', 'June']);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '3';
                }
            };

            $result = IntegerMonth::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerMonth::class)
                ->and($result->value())->toBe(3);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerMonth::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerMonth::fromNull(null))
                ->toThrow(MonthIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerMonth::toNull())
                ->toThrow(MonthIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
