<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\HourIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerHour;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(IntegerHour::class);

describe('IntegerHour', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0-23', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->value())->toBe($value);
        })->with(range(0, 23));

        it('throws for values outside 0-23', function (int $invalidValue): void {
            expect(fn() => new IntegerHour($invalidValue))
                ->toThrow(HourIntegerTypeException::class, 'Expected value between 0-23');
        })->with([-1, 24, 100]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0-23', function (int $value): void {
            $hour = IntegerHour::fromInt($value);
            expect($hour->value())->toBe($value);
        })->with(range(0, 23));

        it('throws for values outside 0-23', function (int $invalidValue): void {
            expect(fn() => IntegerHour::fromInt($invalidValue))
                ->toThrow(HourIntegerTypeException::class, 'Expected value between 0-23');
        })->with([-1, 24, 100]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0-23', function (string $value, int $expected): void {
            $hour = IntegerHour::fromString($value);
            expect($hour->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['23', 23],
            ['12', 12],
        ]);

        it('throws HourIntegerTypeException for values outside 0-23', function (string $invalidValue): void {
            expect(fn() => IntegerHour::fromString($invalidValue))
                ->toThrow(HourIntegerTypeException::class, 'Expected value between 0-23');
        })->with(['-1', '24', '100']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerHour::fromString($invalidValue))
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
            $hour = IntegerHour::fromBool(true);
            expect($hour->value())->toBe(1);
        });

        it('creates instance from false (0)', function (): void {
            $hour = IntegerHour::fromBool(false);
            expect($hour->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0-23', function (float $value, int $expected): void {
            $hour = IntegerHour::fromFloat($value);
            expect($hour->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [23.0, 23],
            [12.0, 12],
        ]);

        it('throws for float values outside 0-23', function (float $invalidValue): void {
            expect(fn() => IntegerHour::fromFloat($invalidValue))
                ->toThrow(HourIntegerTypeException::class, 'Expected value between 0-23');
        })->with([-1.0, 24.0, 100.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerHour::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0-23', function (string $value, int $expected): void {
            $hour = IntegerHour::fromDecimal($value);
            expect($hour->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['23.0', 23],
            ['12.0', 12],
        ]);

        it('throws for decimal values outside 0-23', function (string $invalidValue): void {
            expect(fn() => IntegerHour::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-1.0', '24.0', '100.0', '1.1', '22.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerHour::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerHour for valid values 0-23', function (int $value): void {
            $result = IntegerHour::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe($value);
        })->with(range(0, 23));

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerHour::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 24, 100]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerHour for valid integer strings 0-23', function (string $value, int $expected): void {
            $result = IntegerHour::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['23', 23],
        ]);

        it('returns Undefined for values outside 0-23', function (string $invalidValue): void {
            $result = IntegerHour::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '24', '100']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerHour::tryFromString($invalidValue);
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
        it('returns IntegerHour from true', function (): void {
            $result = IntegerHour::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerHour from false', function (): void {
            $result = IntegerHour::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe(0);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerHour from float with exact integer value 0-23', function (float $value, int $expected): void {
            $result = IntegerHour::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [23.0, 23],
            [12.0, 12],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerHour::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1.0, 24.0, 3.14, 100.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerHour from decimal string with exact integer value 0-23', function (string $value, int $expected): void {
            $result = IntegerHour::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['23.0', 23],
            ['12.0', 12],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerHour::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1.0', '24.0', '1.1', '22.1', '5', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerHour for valid integer inputs 0-23', function (mixed $value, int $expected): void {
            $result = IntegerHour::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0, 0],
            [23, 23],
            ['12', 12],
            [true, 1],
            [false, 0],
            [4.0, 4],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerHour::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [24],
            ['-1'],
            ['24'],
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
            $hour = new IntegerHour($value);
            expect($hour->toInt())->toBe($value);
        })->with([0, 23, 12]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $hour = new IntegerHour($value);
            expect($hour->toString())->toBe($expected)
                ->and((string) $hour)->toBe($expected);
        })->with([
            [0, '0'],
            [23, '23'],
            [12, '12'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->toFloat())->toBe((float) $value)
                ->and($hour->toFloat())->toBeFloat();
        })->with([0, 23, 12]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $hour = new IntegerHour($value);
            expect($hour->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
            [23, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $hour = new IntegerHour($value);
            expect($hour->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [23, '23.0'],
            [12, '12.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->jsonSerialize())->toBe($value)
                ->and($hour->jsonSerialize())->toBeInt();
        })->with([0, 23, 12]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->isEmpty())->toBeFalse();
        })->with([0, 23, 12]);

        it('isUndefined always returns false', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->isUndefined())->toBeFalse();
        })->with([0, 23, 12]);

        it('isTypeOf returns true for matching class', function (): void {
            $hour = IntegerHour::fromInt(5);
            expect($hour->isTypeOf(IntegerHour::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $hour = IntegerHour::fromInt(5);
            expect($hour->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $hour = IntegerHour::fromInt(5);
            expect($hour->isTypeOf('NonExistentClass', IntegerHour::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $hour = new IntegerHour($value);
            expect($hour->value())->toBe($value);
        })->with([0, 23, 12]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerHour::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerHour::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 23, 12]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerHour::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerHour::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['0', '23', '12']);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles multiple round-trips for all valid values', function (int $original): void {
            $result = IntegerHour::fromString(
                IntegerHour::fromInt(
                    IntegerHour::fromString(
                        IntegerHour::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([0, 23, 12]);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '14';
                }
            };

            $result = IntegerHour::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerHour::class)
                ->and($result->value())->toBe(14);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerHour::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });
});
