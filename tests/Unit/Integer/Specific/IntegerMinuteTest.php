<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\MinuteIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerMinute;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(IntegerMinute::class);

describe('IntegerMinute', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0-59', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->value())->toBe($value);
        })->with(range(0, 59));

        it('throws for values outside 0-59', function (int $invalidValue): void {
            expect(fn() => new IntegerMinute($invalidValue))
                ->toThrow(MinuteIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1, 60, 100]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0-59', function (int $value): void {
            $minute = IntegerMinute::fromInt($value);
            expect($minute->value())->toBe($value);
        })->with(range(0, 59));

        it('throws for values outside 0-59', function (int $invalidValue): void {
            expect(fn() => IntegerMinute::fromInt($invalidValue))
                ->toThrow(MinuteIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1, 60, 100]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0-59', function (string $value, int $expected): void {
            $minute = IntegerMinute::fromString($value);
            expect($minute->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['59', 59],
            ['30', 30],
        ]);

        it('throws MinuteIntegerTypeException for values outside 0-59', function (string $invalidValue): void {
            expect(fn() => IntegerMinute::fromString($invalidValue))
                ->toThrow(MinuteIntegerTypeException::class, 'Expected value between 0-59');
        })->with(['-1', '60', '100']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerMinute::fromString($invalidValue))
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
            $minute = IntegerMinute::fromBool(true);
            expect($minute->value())->toBe(1);
        });

        it('creates instance from false (0)', function (): void {
            $minute = IntegerMinute::fromBool(false);
            expect($minute->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0-59', function (float $value, int $expected): void {
            $minute = IntegerMinute::fromFloat($value);
            expect($minute->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [59.0, 59],
            [30.0, 30],
        ]);

        it('throws for float values outside 0-59', function (float $invalidValue): void {
            expect(fn() => IntegerMinute::fromFloat($invalidValue))
                ->toThrow(MinuteIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1.0, 60.0, 100.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerMinute::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0-59', function (string $value, int $expected): void {
            $minute = IntegerMinute::fromDecimal($value);
            expect($minute->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['59.0', 59],
            ['30.0', 30],
        ]);

        it('throws for decimal values outside 0-59', function (string $invalidValue): void {
            expect(fn() => IntegerMinute::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-1.0', '60.0', '100.0', '1.1', '58.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerMinute::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerMinute for valid values 0-59', function (int $value): void {
            $result = IntegerMinute::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe($value);
        })->with(range(0, 59));

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerMinute::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 60, 100]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerMinute for valid integer strings 0-59', function (string $value, int $expected): void {
            $result = IntegerMinute::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['59', 59],
        ]);

        it('returns Undefined for values outside 0-59', function (string $invalidValue): void {
            $result = IntegerMinute::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '60', '100']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerMinute::tryFromString($invalidValue);
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
        it('returns IntegerMinute from true', function (): void {
            $result = IntegerMinute::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerMinute from false', function (): void {
            $result = IntegerMinute::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe(0);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerMinute from float with exact integer value 0-59', function (float $value, int $expected): void {
            $result = IntegerMinute::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [59.0, 59],
            [30.0, 30],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerMinute::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1.0, 60.0, 3.14, 100.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerMinute from decimal string with exact integer value 0-59', function (string $value, int $expected): void {
            $result = IntegerMinute::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['59.0', 59],
            ['30.0', 30],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerMinute::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1.0', '60.0', '1.1', '58.1', '5', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerMinute for valid integer inputs 0-59', function (mixed $value, int $expected): void {
            $result = IntegerMinute::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0, 0],
            [59, 59],
            ['30', 30],
            [true, 1],
            [false, 0],
            [4.0, 4],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerMinute::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-1],
            [60],
            ['-1'],
            ['60'],
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
            $minute = new IntegerMinute($value);
            expect($minute->toInt())->toBe($value);
        })->with([0, 59, 30]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $minute = new IntegerMinute($value);
            expect($minute->toString())->toBe($expected)
                ->and((string) $minute)->toBe($expected);
        })->with([
            [0, '0'],
            [59, '59'],
            [30, '30'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->toFloat())->toBe((float) $value)
                ->and($minute->toFloat())->toBeFloat();
        })->with([0, 59, 30]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $minute = new IntegerMinute($value);
            expect($minute->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
            [59, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $minute = new IntegerMinute($value);
            expect($minute->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [59, '59.0'],
            [30, '30.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->jsonSerialize())->toBe($value)
                ->and($minute->jsonSerialize())->toBeInt();
        })->with([0, 59, 30]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->isEmpty())->toBeFalse();
        })->with([0, 59, 30]);

        it('isUndefined always returns false', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->isUndefined())->toBeFalse();
        })->with([0, 59, 30]);

        it('isTypeOf returns true for matching class', function (): void {
            $minute = IntegerMinute::fromInt(5);
            expect($minute->isTypeOf(IntegerMinute::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $minute = IntegerMinute::fromInt(5);
            expect($minute->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $minute = IntegerMinute::fromInt(5);
            expect($minute->isTypeOf('NonExistentClass', IntegerMinute::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $minute = new IntegerMinute($value);
            expect($minute->value())->toBe($value);
        })->with([0, 59, 30]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerMinute::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerMinute::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 59, 30]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerMinute::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerMinute::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['0', '59', '30']);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles multiple round-trips for all valid values', function (int $original): void {
            $result = IntegerMinute::fromString(
                IntegerMinute::fromInt(
                    IntegerMinute::fromString(
                        IntegerMinute::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([0, 59, 30]);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '45';
                }
            };

            $result = IntegerMinute::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerMinute::class)
                ->and($result->value())->toBe(45);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerMinute::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });
});
