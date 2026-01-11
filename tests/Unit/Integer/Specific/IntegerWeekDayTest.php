<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerWeekDay;
use PhpTypedValues\Undefined\Alias\Undefined;

// ============================================
// CONSTRUCTOR & FACTORY METHODS
// ============================================

describe('Constructor', function (): void {
    it('creates instance for valid values 1-7', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->value())->toBe($value);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('throws for values outside 1-7', function (int $invalidValue): void {
        expect(fn() => new IntegerWeekDay($invalidValue))
            ->toThrow(IntegerTypeException::class, 'Expected value between 1-7');
    })->with([0, 8, -1, 10]);
});

describe('fromInt factory', function (): void {
    it('creates instance for valid values 1-7', function (int $value): void {
        $weekday = IntegerWeekDay::fromInt($value);
        expect($weekday->value())->toBe($value);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('throws for values outside 1-7', function (int $invalidValue): void {
        expect(fn() => IntegerWeekDay::fromInt($invalidValue))
            ->toThrow(IntegerTypeException::class, 'Expected value between 1-7');
    })->with([0, 8, -1, 10]);
});

describe('fromString factory', function (): void {
    it('creates instance for valid integer strings 1-7', function (string $value, int $expected): void {
        $weekday = IntegerWeekDay::fromString($value);
        expect($weekday->value())->toBe($expected);
    })->with([
        ['1', 1],
        ['7', 7],
    ]);

    it('throws IntegerTypeException for values outside 1-7', function (string $invalidValue): void {
        expect(fn() => IntegerWeekDay::fromString($invalidValue))
            ->toThrow(IntegerTypeException::class, 'Expected value between 1-7');
    })->with(['0', '8', '-1', '10']);

    it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
        expect(fn() => IntegerWeekDay::fromString($invalidValue))
            ->toThrow($exceptionClass);
    })->with([
        ['5.5', StringTypeException::class],      // Non-integer float - throws StringTypeException
        ['a', StringTypeException::class],        // Non-numeric - throws StringTypeException
        ['', StringTypeException::class],         // Empty string - throws StringTypeException
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
        $weekday = IntegerWeekDay::fromBool(true);
        expect($weekday->value())->toBe(1);
    });

    it('throws from false', function (): void {
        expect(fn() => IntegerWeekDay::fromBool(false))
            ->toThrow(IntegerTypeException::class, 'Expected value between 1-7, got "0"');
    });
});

describe('fromFloat factory', function (): void {
    it('creates instance from float with exact integer value 1-7', function (float $value, int $expected): void {
        $weekday = IntegerWeekDay::fromFloat($value);
        expect($weekday->value())->toBe($expected);
    })->with([
        [1.0, 1],
        [7.0, 7],
        [3.0, 3],
    ]);

    it('throws for float values outside 1-7', function (float $invalidValue): void {
        expect(fn() => IntegerWeekDay::fromFloat($invalidValue))
            ->toThrow(IntegerTypeException::class, 'Expected value between 1-7');
    })->with([0.0, 8.0, -1.0]);

    it('throws FloatTypeException for non-integer floats', function (): void {
        expect(fn() => IntegerWeekDay::fromFloat(3.14))
            ->toThrow(FloatTypeException::class);
    });
});

describe('fromLabel factory', function (): void {
    it('creates instance from valid weekday labels', function (string $label, int $expectedValue): void {
        $weekday = IntegerWeekDay::fromLabel($label);
        expect($weekday->value())->toBe($expectedValue);
    })->with([
        ['Monday', 1],
        ['Tuesday', 2],
        ['Wednesday', 3],
        ['Thursday', 4],
        ['Friday', 5],
        ['Saturday', 6],
        ['Sunday', 7],
    ]);

    it('throws for invalid weekday labels', function (string $invalidLabel): void {
        expect(fn() => IntegerWeekDay::fromLabel($invalidLabel))
            ->toThrow(IntegerTypeException::class, 'Invalid weekday label');
    })->with([
        'monday',       // Lowercase
        'Mon',          // Abbreviation
        '',             // Empty
        'InvalidDay',   // Random
        'monday ',      // Trailing space
    ]);
});

// ============================================
// TRY-FROM METHODS (SAFE FACTORIES)
// ============================================

describe('tryFromInt method', function (): void {
    it('returns IntegerWeekDay for valid values 1-7', function (int $value): void {
        $result = IntegerWeekDay::tryFromInt($value);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe($value);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('returns Undefined for invalid values', function (int $invalidValue): void {
        $result = IntegerWeekDay::tryFromInt($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([0, 8, -1, 10]);
});

describe('tryFromString method', function (): void {
    it('returns IntegerWeekDay for valid integer strings 1-7', function (string $value, int $expected): void {
        $result = IntegerWeekDay::tryFromString($value);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe($expected);
    })->with([
        ['1', 1],
        ['7', 7],
    ]);

    it('returns Undefined for values outside 1-7', function (string $invalidValue): void {
        $result = IntegerWeekDay::tryFromString($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with(['0', '8', '-1', '10']);

    it('returns Undefined for non-integer strings', function (string $invalidValue): void {
        $result = IntegerWeekDay::tryFromString($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([
        '5.5',      // Non-integer float
        'a',        // Non-numeric
        '',         // Empty string
        '3.0',       // Float-looking string
        '5.0',       // Float-looking string
        '01',        // Leading zero
        '+1',        // Plus sign
        ' 1',        // Leading space
        '1 ',        // Trailing space
    ]);
});

describe('tryFromBool method', function (): void {
    it('returns IntegerWeekDay from true', function (): void {
        $result = IntegerWeekDay::tryFromBool(true);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe(1);
    });

    it('returns Undefined from false', function (): void {
        $result = IntegerWeekDay::tryFromBool(false);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});

describe('tryFromFloat method', function (): void {
    it('returns IntegerWeekDay from float with exact integer value 1-7', function (float $value, int $expected): void {
        $result = IntegerWeekDay::tryFromFloat($value);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe($expected);
    })->with([
        [1.0, 1],
        [7.0, 7],
        [3.0, 3],
    ]);

    it('returns Undefined for invalid floats', function (float $invalidValue): void {
        $result = IntegerWeekDay::tryFromFloat($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([0.0, 8.0, 3.14, -1.0]);
});

describe('tryFromMixed method', function (): void {
    it('returns IntegerWeekDay for valid integer inputs 1-7', function (mixed $value, int $expected): void {
        $result = IntegerWeekDay::tryFromMixed($value);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe($expected);
    })->with([
        [1, 1],
        [7, 7],
        ['3', 3],
        [true, 1],      // Boolean true
        [4.0, 4],       // Float
    ]);

    it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
        $result = IntegerWeekDay::tryFromMixed($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([
        // Test each invalid case separately to avoid ArgumentCountError
        [0],              // Int out of range
        [8],              // Int out of range
        [false],          // Boolean false
        ['0'],            // String out of range
        ['8'],            // String out of range
        ['5.5'],          // Non-integer string
        ['a'],            // Non-numeric string
        [''],             // Empty string
        ['7.0'],           // Float-looking string
        ['01'],            // Leading zero
        ['+1'],            // Plus sign
        [' 1'],            // Leading space
        ['1 '],            // Trailing space
        [[]],             // Array
        [null],           // Null
        [new stdClass()], // Object
    ]);
});

// ============================================
// CONVERSION METHODS
// ============================================

describe('Conversion methods', function (): void {
    it('toInt returns integer value', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->toInt())->toBe($value);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('toString returns string representation', function (int $value, string $expected): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->toString())->toBe($expected)
            ->and((string) $weekday)->toBe($expected);
    })->with([
        [1, '1'],
        [7, '7'],
        [3, '3'],
    ]);

    it('toFloat returns float representation', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->toFloat())->toBe((float) $value)
            ->and($weekday->toFloat())->toBeFloat();
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('toBool returns true for all values', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->toBool())->toBeTrue();
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('toLabel returns correct weekday name', function (int $value, string $expectedLabel): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->toLabel())->toBe($expectedLabel);
    })->with([
        [1, 'Monday'],
        [2, 'Tuesday'],
        [3, 'Wednesday'],
        [4, 'Thursday'],
        [5, 'Friday'],
        [6, 'Saturday'],
        [7, 'Sunday'],
    ]);

    it('jsonSerialize returns integer value', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->jsonSerialize())->toBe($value)
            ->and($weekday->jsonSerialize())->toBeInt();
    })->with([1, 2, 3, 4, 5, 6, 7]);
});

// ============================================
// TYPE CHECKS & PROPERTIES
// ============================================

describe('Type checks and properties', function (): void {
    it('isEmpty always returns false', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->isEmpty())->toBeFalse();
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('isUndefined always returns false', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->isUndefined())->toBeFalse();
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('isTypeOf returns true for matching class', function (): void {
        $weekday = IntegerWeekDay::fromInt(5);
        expect($weekday->isTypeOf(IntegerWeekDay::class))->toBeTrue();
    });

    it('isTypeOf returns false for non-matching class', function (): void {
        $weekday = IntegerWeekDay::fromInt(5);
        expect($weekday->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true when at least one class matches', function (): void {
        $weekday = IntegerWeekDay::fromInt(5);
        expect($weekday->isTypeOf('NonExistentClass', IntegerWeekDay::class, 'AnotherClass'))->toBeTrue();
    });

    it('value() returns integer value', function (int $value): void {
        $weekday = new IntegerWeekDay($value);
        expect($weekday->value())->toBe($value);
    })->with([1, 2, 3, 4, 5, 6, 7]);
});

// ============================================
// ROUND-TRIP CONVERSIONS
// ============================================

describe('Round-trip conversions', function (): void {
    it('preserves value through int → string → int conversion', function (int $original): void {
        $v1 = IntegerWeekDay::fromInt($original);
        $str = $v1->toString();
        $v2 = IntegerWeekDay::fromString($str);
        expect($v2->value())->toBe($original);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('preserves value through string → int → string conversion', function (string $original): void {
        $v1 = IntegerWeekDay::fromString($original);
        $int = $v1->toInt();
        $v2 = IntegerWeekDay::fromInt($int);
        expect($v2->toString())->toBe($original);
    })->with(['1', '2', '3', '4', '5', '6', '7']);

    it('preserves value through label → int → label conversion', function (string $label, int $expectedValue): void {
        $weekday = IntegerWeekDay::fromLabel($label);
        $value = $weekday->value();
        $resultLabel = IntegerWeekDay::fromInt($value)->toLabel();
        expect($resultLabel)->toBe($label)
            ->and($value)->toBe($expectedValue);
    })->with([
        ['Monday', 1],
        ['Tuesday', 2],
        ['Wednesday', 3],
        ['Thursday', 4],
        ['Friday', 5],
        ['Saturday', 6],
        ['Sunday', 7],
    ]);

    it('preserves value through int → label → int conversion', function (int $value, string $expectedLabel): void {
        $weekday = IntegerWeekDay::fromInt($value);
        $label = $weekday->toLabel();
        $resultValue = IntegerWeekDay::fromLabel($label)->value();
        expect($label)->toBe($expectedLabel)
            ->and($resultValue)->toBe($value);
    })->with([
        [1, 'Monday'],
        [2, 'Tuesday'],
        [3, 'Wednesday'],
        [4, 'Thursday'],
        [5, 'Friday'],
        [6, 'Saturday'],
        [7, 'Sunday'],
    ]);
});

// ============================================
// EDGE CASES & COMPREHENSIVE TESTS
// ============================================

describe('Edge cases and comprehensive tests', function (): void {
    it('handles multiple round-trips for all valid values', function (int $original): void {
        // int → string → int → string → int
        $result = IntegerWeekDay::fromString(
            IntegerWeekDay::fromInt(
                IntegerWeekDay::fromString(
                    IntegerWeekDay::fromInt($original)->toString()
                )->toInt()
            )->toString()
        )->value();

        expect($result)->toBe($original);
    })->with([1, 2, 3, 4, 5, 6, 7]);

    it('handles multiple round-trips for all labels', function (string $label): void {
        // label → int → label → int → label
        $result = IntegerWeekDay::fromInt(
            IntegerWeekDay::fromLabel(
                IntegerWeekDay::fromInt(
                    IntegerWeekDay::fromLabel($label)->value()
                )->toLabel()
            )->value()
        )->toLabel();

        expect($result)->toBe($label);
    })->with(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);

    // Test Stringable objects
    it('handles Stringable objects', function (): void {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '3';
            }
        };

        $result = IntegerWeekDay::tryFromMixed($stringable);
        expect($result)->toBeInstanceOf(IntegerWeekDay::class)
            ->and($result->value())->toBe(3);
    });

    // Test that fromMixed correctly catches TypeException
    it('tryFromMixed catches TypeException for unserializable types', function (): void {
        $result = IntegerWeekDay::tryFromMixed([]);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
