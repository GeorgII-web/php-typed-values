<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\MariaDb\IntegerTiny;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerTiny', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values -128..127', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->value())->toBe($value);
        })->with([-128, -1, 0, 1, 127]);

        it('throws for values outside -128..127', function (int $invalidValue): void {
            expect(fn() => new IntegerTiny($invalidValue))
                ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127');
        })->with([-129, 128, -200, 200]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values -128..127', function (int $value): void {
            $tiny = IntegerTiny::fromInt($value);
            expect($tiny->value())->toBe($value);
        })->with([-128, -1, 0, 1, 127]);

        it('throws for values outside -128..127', function (int $invalidValue): void {
            expect(fn() => IntegerTiny::fromInt($invalidValue))
                ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127');
        })->with([-129, 128, -200, 200]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings -128..127', function (string $value, int $expected): void {
            $tiny = IntegerTiny::fromString($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            ['-128', -128],
            ['0', 0],
            ['127', 127],
            ['-5', -5],
        ]);

        it('throws IntegerTypeException for values outside -128..127', function (string $invalidValue): void {
            expect(fn() => IntegerTiny::fromString($invalidValue))
                ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127');
        })->with(['-129', '128', '-200', '200']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerTiny::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['12.3', StringTypeException::class],  // Non-integer float - throws StringTypeException
            ['5.5', StringTypeException::class],   // Non-integer float - throws StringTypeException
            ['a', StringTypeException::class],     // Non-numeric - throws StringTypeException
            ['', StringTypeException::class],      // Empty string - throws StringTypeException
            ['3.0', StringTypeException::class],   // Float-looking strings that represent integers are now rejected
            ['5.0', StringTypeException::class],
            ['01', StringTypeException::class],    // Leading zeros are now rejected
            ['+1', StringTypeException::class],    // Plus sign is now rejected
            [' 1', StringTypeException::class],    // Leading space is now rejected
            ['1 ', StringTypeException::class],    // Trailing space is now rejected
        ]);
    });

    describe('fromBool factory', function (): void {
        it('creates instance from true', function (): void {
            $tiny = IntegerTiny::fromBool(true);
            expect($tiny->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $tiny = IntegerTiny::fromBool(false);
            expect($tiny->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value -128..127', function (float $value, int $expected): void {
            $tiny = IntegerTiny::fromFloat($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            [-128.0, -128],
            [0.0, 0],
            [127.0, 127],
            [5.0, 5],
        ]);

        it('throws for float values outside -128..127', function (float $invalidValue): void {
            expect(fn() => IntegerTiny::fromFloat($invalidValue))
                ->toThrow(IntegerTypeException::class, 'Expected tiny integer in range -128..127');
        })->with([-129.0, 128.0, -200.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerTiny::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings -128..127', function (string $value, int $expected): void {
            $tiny = IntegerTiny::fromDecimal($value);
            expect($tiny->value())->toBe($expected);
        })->with([
            ['-128.0', -128],
            ['0.0', 0],
            ['127.0', 127],
            ['5.0', 5],
        ]);

        it('throws for decimal values outside -128..127', function (string $invalidValue): void {
            expect(fn() => IntegerTiny::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-128.1', '127.1', '-129.0', '128.0']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerTiny::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerTiny for valid values -128..127', function (int $value): void {
            $result = IntegerTiny::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe($value);
        })->with([-128, -1, 0, 1, 127]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerTiny::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-129, 128, -200, 200]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerTiny for valid integer strings -128..127', function (string $value, int $expected): void {
            $result = IntegerTiny::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-128', -128],
            ['0', 0],
            ['127', 127],
        ]);

        it('returns Undefined for values outside -128..127', function (string $invalidValue): void {
            $result = IntegerTiny::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-129', '128', '-200', '200']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerTiny::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            '12.3',      // Non-integer float
            '5.5',       // Non-integer float
            'a',         // Non-numeric
            '',          // Empty string
            '3.0',       // Float-looking string
            '5.0',       // Float-looking string
            '01',        // Leading zero
            '+1',        // Plus sign
            ' 1',        // Leading space
            '1 ',        // Trailing space
        ]);
    });

    describe('tryFromBool method', function (): void {
        it('returns IntegerTiny from true', function (): void {
            $result = IntegerTiny::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerTiny from false', function (): void {
            $result = IntegerTiny::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe(0);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerTiny from float with exact integer value -128..127', function (float $value, int $expected,
        ): void {
            $result = IntegerTiny::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-128.0, -128],
            [0.0, 0],
            [127.0, 127],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerTiny::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-129.0, 128.0, 3.14, -200.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerTiny from decimal string with exact integer value -128..127', function (string $value, int $expected): void {
            $result = IntegerTiny::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-128.0', -128],
            ['0.0', 0],
            ['127.0', 127],
            ['5.0', 5],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerTiny::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-128.1', '127.1', '-129.0', '128.0', '42', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerTiny for valid integer inputs -128..127', function (mixed $value, int $expected): void {
            $result = IntegerTiny::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-1, -1],
            [127, 127],
            ['-5', -5],
            ['0', 0],
            [true, 1],      // Boolean true
            [false, 0],     // Boolean false
            [5.0, 5],       // Float
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerTiny::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            // Test each invalid case separately to avoid ArgumentCountError
            [-129],            // Int out of range
            [128],             // Int out of range
            ['-129'],          // String out of range
            ['128'],           // String out of range
            ['12.3'],          // Non-integer string
            ['5.5'],           // Non-integer string
            ['a'],             // Non-numeric string
            [''],              // Empty string
            ['7.0'],           // Float-looking string
            ['01'],            // Leading zero
            ['+1'],            // Plus sign
            [' 1'],            // Leading space
            ['1 '],            // Trailing space
            [[]],              // Array
            [null],            // Null
            [new stdClass()],  // Object
        ]);
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion methods', function (): void {
        it('toInt returns integer value', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->toInt())->toBe($value);
        })->with([-128, -1, 0, 1, 127]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->toString())->toBe($expected)
                ->and((string) $tiny)->toBe($expected);
        })->with([
            [-128, '-128'],
            [0, '0'],
            [127, '127'],
            [-5, '-5'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->toFloat())->toBe((float) $value)
                ->and($tiny->toFloat())->toBeFloat();
        })->with([-128, -1, 0, 1, 127]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->toBool())->toBe($expected);
        })->with([
            [-128, true],
            [-1, true],
            [0, false],
            [1, true],
            [127, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->toDecimal())->toBe($expected);
        })->with([
            [-128, '-128.0'],
            [0, '0.0'],
            [127, '127.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->jsonSerialize())->toBe($value)
                ->and($tiny->jsonSerialize())->toBeInt();
        })->with([-128, -1, 0, 1, 127]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->isEmpty())->toBeFalse();
        })->with([-128, -1, 0, 1, 127]);

        it('isUndefined always returns false', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->isUndefined())->toBeFalse();
        })->with([-128, -1, 0, 1, 127]);

        it('isTypeOf returns true for matching class', function (): void {
            $tiny = IntegerTiny::fromInt(5);
            expect($tiny->isTypeOf(IntegerTiny::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $tiny = IntegerTiny::fromInt(5);
            expect($tiny->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $tiny = IntegerTiny::fromInt(5);
            expect($tiny->isTypeOf('NonExistentClass', IntegerTiny::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $tiny = new IntegerTiny($value);
            expect($tiny->value())->toBe($value);
        })->with([-128, -1, 0, 1, 127]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerTiny::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerTiny::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([-128, -50, 0, 50, 127]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerTiny::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerTiny::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['-128', '-50', '0', '50', '127']);

        it('handles multiple round-trips for boundary values', function (int $original): void {
            // int → string → int → string → int
            $result = IntegerTiny::fromString(
                IntegerTiny::fromInt(
                    IntegerTiny::fromString(
                        IntegerTiny::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([-128, -1, 0, 1, 127]);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        // Test Stringable objects
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerTiny::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerTiny::class)
                ->and($result->value())->toBe(42);
        });

        // Test boundary edge cases
        it('handles all boundary values correctly', function (): void {
            $min = new IntegerTiny(-128);
            $zero = new IntegerTiny(0);
            $max = new IntegerTiny(127);

            expect($min->value())->toBe(-128)
                ->and($min->toString())->toBe('-128')
                ->and($min->toInt())->toBe(-128)
                ->and($min->toFloat())->toBe(-128.0)
                ->and($min->toBool())->toBeTrue()
                ->and($zero->value())->toBe(0)
                ->and($zero->toString())->toBe('0')
                ->and($zero->toInt())->toBe(0)
                ->and($zero->toFloat())->toBe(0.0)
                ->and($zero->toBool())->toBeFalse()
                ->and($max->value())->toBe(127)
                ->and($max->toString())->toBe('127')
                ->and($max->toInt())->toBe(127)
                ->and($max->toFloat())->toBe(127.0)
                ->and($max->toBool())->toBeTrue();
        });

        // Test that fromMixed correctly catches TypeException
        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerTiny::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });

        it('IntegerTiny::tryFrom* methods return default on failure', function (): void {
            expect(IntegerTiny::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTiny::tryFromFloat(128.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTiny::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTiny::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTiny::tryFromString('128'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerTiny::tryFromInt(128))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerTinyTest extends IntegerTiny
    {
        public static function fromBool(bool $value): static
        {
            throw new Exception('test');
        }
    }

    describe('IntegerTiny catch block coverage', function (): void {
        it('IntegerTiny::tryFromBool catch block coverage', function (): void {
            expect(IntegerTinyTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });
    });
});
