<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\YearIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerYear;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(IntegerYear::class);

describe('IntegerYear', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 1-9999', function (int $value): void {
            $year = new IntegerYear($value);
            expect($year->value())->toBe($value);
        })->with([1, 100, 2024, 9999]);

        it('throws for values outside 1-9999', function (int $invalidValue): void {
            expect(fn() => new IntegerYear($invalidValue))
                ->toThrow(YearIntegerTypeException::class, 'Expected value between 1-9999');
        })->with([0, 10000, -1]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 1-9999', function (int $value): void {
            $year = IntegerYear::fromInt($value);
            expect($year->value())->toBe($value);
        })->with([1, 100, 2024, 9999]);

        it('throws for values outside 1-9999', function (int $invalidValue): void {
            expect(fn() => IntegerYear::fromInt($invalidValue))
                ->toThrow(YearIntegerTypeException::class, 'Expected value between 1-9999');
        })->with([0, 10000, -1]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 1-9999', function (string $value, int $expected): void {
            $year = IntegerYear::fromString($value);
            expect($year->value())->toBe($expected);
        })->with([
            ['1', 1],
            ['2024', 2024],
            ['9999', 9999],
        ]);

        it('throws YearIntegerTypeException for values outside 1-9999', function (string $invalidValue): void {
            expect(fn() => IntegerYear::fromString($invalidValue))
                ->toThrow(YearIntegerTypeException::class, 'Expected value between 1-9999');
        })->with(['0', '10000', '-1']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerYear::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['2024.5', StringTypeException::class],
            ['abc', StringTypeException::class],
            ['', StringTypeException::class],
            ['01', StringTypeException::class],
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true (1)', function (): void {
            $year = IntegerYear::fromBool(true);
            expect($year->value())->toBe(1);
        });

        it('throws from false (0)', function (): void {
            expect(fn() => IntegerYear::fromBool(false))
                ->toThrow(YearIntegerTypeException::class, 'Expected value between 1-9999');
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from integer float 1-9999', function (float $value, int $expected): void {
            $year = IntegerYear::fromFloat($value);
            expect($year->value())->toBe($expected);
        })->with([
            [1.0, 1],
            [2024.0, 2024],
            [9999.0, 9999],
        ]);

        it('throws for float values outside 1-9999', function (float $invalidValue): void {
            expect(fn() => IntegerYear::fromFloat($invalidValue))
                ->toThrow(YearIntegerTypeException::class, 'Expected value between 1-9999');
        })->with([0.0, 10000.0, -1.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerYear::fromFloat(2024.5))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 1-9999', function (string $value, int $expected): void {
            $year = IntegerYear::fromDecimal($value);
            expect($year->value())->toBe($expected);
        })->with([
            ['1.0', 1],
            ['2024.0', 2024],
            ['9999.0', 9999],
        ]);

        it('throws for decimal values outside 1-9999', function (string $invalidValue): void {
            expect(fn() => IntegerYear::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['0.0', '10000.0', '-1.0', '2024.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerYear::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['2024', 'abc', '']);
    });

    // ============================================
    // SAFE FACTORY METHODS (TRY-FROM)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerYear for valid value', function (): void {
            $result = IntegerYear::tryFromInt(2024);
            expect($result)->toBeInstanceOf(IntegerYear::class)
                ->and($result->value())->toBe(2024);
        });

        it('returns Undefined for invalid value', function (): void {
            $result = IntegerYear::tryFromInt(0);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerYear for valid string', function (): void {
            $result = IntegerYear::tryFromString('2024');
            expect($result)->toBeInstanceOf(IntegerYear::class)
                ->and($result->value())->toBe(2024);
        });

        it('returns Undefined for invalid string', function (): void {
            $result = IntegerYear::tryFromString('abc');
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerYear for valid types', function (mixed $value, int $expected): void {
            $result = IntegerYear::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerYear::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [2024, 2024],
            ['2025', 2025],
            [2026.0, 2026],
            [true, 1],
        ]);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '2024';
                }
            };
            $result = IntegerYear::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerYear::class)
                ->and($result->value())->toBe(2024);
        });

        it('returns Undefined for invalid inputs', function (mixed $value): void {
            $result = IntegerYear::tryFromMixed($value);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [0],
            ['abc'],
            [2024.5],
            [false],
            [[]],
            [null],
            [new stdClass()],
        ]);
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('toInt returns integer', function (): void {
            $year = new IntegerYear(2024);
            expect($year->toInt())->toBe(2024);
        });

        it('toString returns string', function (): void {
            $year = new IntegerYear(2024);
            expect($year->toString())->toBe('2024')
                ->and((string) $year)->toBe('2024');
        });

        it('toFloat returns float', function (): void {
            $year = new IntegerYear(2024);
            expect($year->toFloat())->toBe(2024.0);
        });

        it('toBool returns true', function (): void {
            $year = new IntegerYear(2024);
            expect($year->toBool())->toBeTrue();
        });

        it('toDecimal returns decimal string', function (): void {
            $year = new IntegerYear(2024);
            expect($year->toDecimal())->toBe('2024.0');
        });

        it('jsonSerialize returns integer', function (): void {
            $year = new IntegerYear(2024);
            expect($year->jsonSerialize())->toBe(2024);
        });
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty returns false', function (): void {
            $year = new IntegerYear(2024);
            expect($year->isEmpty())->toBeFalse();
        });

        it('isUndefined returns false', function (): void {
            $year = new IntegerYear(2024);
            expect($year->isUndefined())->toBeFalse();
        });

        it('isTypeOf works correctly', function (): void {
            $year = new IntegerYear(2024);
            expect($year->isTypeOf(IntegerYear::class))->toBeTrue()
                ->and($year->isTypeOf('NonExistent'))->toBeFalse();
        });

        it('value() returns integer', function (): void {
            $year = new IntegerYear(2024);
            expect($year->value())->toBe(2024);
        });
    });
});
