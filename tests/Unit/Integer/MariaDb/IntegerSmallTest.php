<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\SmallIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\MariaDb\IntegerSmall;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerSmall::class);

describe('IntegerSmall', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values -32768..32767', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->value())->toBe($value);
        })->with([-32768, -1, 0, 1, 32767]);

        it('throws for values outside -32768..32767', function (int $invalidValue): void {
            expect(fn() => new IntegerSmall($invalidValue))
                ->toThrow(SmallIntegerTypeException::class, 'Expected small integer in range -32768..32767');
        })->with([-32769, 32768, -40000, 40000]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values -32768..32767', function (int $value): void {
            $small = IntegerSmall::fromInt($value);
            expect($small->value())->toBe($value);
        })->with([-32768, -1, 0, 1, 32767]);

        it('throws for values outside -32768..32767', function (int $invalidValue): void {
            expect(fn() => IntegerSmall::fromInt($invalidValue))
                ->toThrow(SmallIntegerTypeException::class, 'Expected small integer in range -32768..32767');
        })->with([-32769, 32768, -40000, 40000]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings -32768..32767', function (string $value, int $expected): void {
            $small = IntegerSmall::fromString($value);
            expect($small->value())->toBe($expected);
        })->with([
            ['-32768', -32768],
            ['0', 0],
            ['32767', 32767],
            ['-5', -5],
        ]);

        it('throws SmallIntegerTypeException for values outside -32768..32767', function (string $invalidValue): void {
            expect(fn() => IntegerSmall::fromString($invalidValue))
                ->toThrow(SmallIntegerTypeException::class, 'Expected small integer in range -32768..32767');
        })->with(['-32769', '32768', '-40000', '40000']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerSmall::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
            ['12.3', StringTypeException::class],
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
            $small = IntegerSmall::fromBool(true);
            expect($small->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $small = IntegerSmall::fromBool(false);
            expect($small->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value -32768..32767', function (float $value, int $expected): void {
            $small = IntegerSmall::fromFloat($value);
            expect($small->value())->toBe($expected);
        })->with([
            [-32768.0, -32768],
            [0.0, 0],
            [32767.0, 32767],
            [5.0, 5],
        ]);

        it('throws for float values outside -32768..32767', function (float $invalidValue): void {
            expect(fn() => IntegerSmall::fromFloat($invalidValue))
                ->toThrow(SmallIntegerTypeException::class, 'Expected small integer in range -32768..32767');
        })->with([-32769.0, 32768.0, -40000.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerSmall::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings -32768..32767', function (string $value, int $expected): void {
            $small = IntegerSmall::fromDecimal($value);
            expect($small->value())->toBe($expected);
        })->with([
            ['-32768.0', -32768],
            ['0.0', 0],
            ['32767.0', 32767],
            ['5.0', 5],
        ]);

        it('throws for decimal values outside -32768..32767', function (string $invalidValue): void {
            expect(fn() => IntegerSmall::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-32768.1', '32767.1', '-32769.0', '32768.0']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerSmall::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerSmall for valid values -32768..32767', function (int $value): void {
            $result = IntegerSmall::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe($value);
        })->with([-32768, -1, 0, 1, 32767]);

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerSmall::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-32769, 32768, -40000, 40000]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerSmall for valid integer strings -32768..32767', function (string $value, int $expected): void {
            $result = IntegerSmall::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-32768', -32768],
            ['0', 0],
            ['32767', 32767],
        ]);

        it('returns Undefined for values outside -32768..32767', function (string $invalidValue): void {
            $result = IntegerSmall::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-32769', '32768', '-40000', '40000']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerSmall::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            '12.3',
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
        it('returns IntegerSmall from true', function (): void {
            $result = IntegerSmall::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerSmall from false', function (): void {
            $result = IntegerSmall::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe(0);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerSmall from float with exact integer value -32768..32767', function (
            float $value,
            int $expected,
        ): void {
            $result = IntegerSmall::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-32768.0, -32768],
            [0.0, 0],
            [32767.0, 32767],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerSmall::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-32769.0, 32768.0, 3.14, -40000.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerSmall from decimal string with exact integer value -32768..32767', function (string $value, int $expected): void {
            $result = IntegerSmall::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-32768.0', -32768],
            ['0.0', 0],
            ['32767.0', 32767],
            ['5.0', 5],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerSmall::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-32768.1', '32767.1', '-32769.0', '32768.0', '42', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerSmall for valid integer inputs -32768..32767', function (mixed $value, int $expected): void {
            $result = IntegerSmall::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-1, -1],
            [32767, 32767],
            ['-5', -5],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerSmall::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            [-32769],
            [32768],
            ['-32769'],
            ['32768'],
            ['12.3'],
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
            $small = new IntegerSmall($value);
            expect($small->toInt())->toBe($value);
        })->with([-32768, -1, 0, 1, 32767]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $small = new IntegerSmall($value);
            expect($small->toString())->toBe($expected)
                ->and((string) $small)->toBe($expected);
        })->with([
            [-32768, '-32768'],
            [0, '0'],
            [32767, '32767'],
            [-5, '-5'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->toFloat())->toBe((float) $value)
                ->and($small->toFloat())->toBeFloat();
        })->with([-32768, -1, 0, 1, 32767]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $small = new IntegerSmall($value);
            expect($small->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $small = new IntegerSmall($value);
            expect($small->toDecimal())->toBe($expected);
        })->with([
            [-32768, '-32768.0'],
            [0, '0.0'],
            [32767, '32767.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->jsonSerialize())->toBe($value)
                ->and($small->jsonSerialize())->toBeInt();
        })->with([-32768, -1, 0, 1, 32767]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->isEmpty())->toBeFalse();
        })->with([-32768, -1, 0, 1, 32767]);

        it('isUndefined always returns false', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->isUndefined())->toBeFalse();
        })->with([-32768, -1, 0, 1, 32767]);

        it('isTypeOf returns true for matching class', function (): void {
            $small = IntegerSmall::fromInt(5);
            expect($small->isTypeOf(IntegerSmall::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $small = IntegerSmall::fromInt(5);
            expect($small->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $small = IntegerSmall::fromInt(5);
            expect($small->isTypeOf('NonExistentClass', IntegerSmall::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $small = new IntegerSmall($value);
            expect($small->value())->toBe($value);
        })->with([-32768, -1, 0, 1, 32767]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerSmall::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerSmall::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([-32768, -50, 0, 50, 32767]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerSmall::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerSmall::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['-32768', '-50', '0', '50', '32767']);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            };

            $result = IntegerSmall::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerSmall::class)
                ->and($result->value())->toBe(42);
        });

        it('handles all boundary values correctly', function (): void {
            $min = new IntegerSmall(-32768);
            $zero = new IntegerSmall(0);
            $max = new IntegerSmall(32767);

            expect($min->value())->toBe(-32768)
                ->and($min->toString())->toBe('-32768')
                ->and($min->toInt())->toBe(-32768)
                ->and($min->toFloat())->toBe(-32768.0)
                ->and($zero->value())->toBe(0)
                ->and($zero->toString())->toBe('0')
                ->and($zero->toInt())->toBe(0)
                ->and($zero->toFloat())->toBe(0.0)
                ->and($zero->toBool())->toBeFalse()
                ->and($max->value())->toBe(32767)
                ->and($max->toString())->toBe('32767')
                ->and($max->toInt())->toBe(32767)
                ->and($max->toFloat())->toBe(32767.0);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerSmall::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });

        it('IntegerSmall::tryFrom* methods return default on failure', function (): void {
            expect(IntegerSmall::tryFromFloat(1.5))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmall::tryFromFloat(40000.0))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmall::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmall::tryFromString('abc'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmall::tryFromString('40000'))->toBeInstanceOf(Undefined::class)
                ->and(IntegerSmall::tryFromInt(40000))->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerSmallTest extends IntegerSmall
    {
        public static function fromBool(bool $value): static
        {
            throw new Exception('test');
        }
    }

    describe('IntegerSmall catch block coverage', function (): void {
        it('IntegerSmall::tryFromBool catch block coverage', function (): void {
            expect(IntegerSmallTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });
    });
});
