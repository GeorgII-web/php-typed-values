<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\SecondIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerSecond;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

use function sprintf;

covers(IntegerSecond::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class IntegerSecondTest extends IntegerSecond
{
    public function __construct(int $value)
    {
        if ($value < 1) {
            throw new SecondIntegerTypeException(sprintf('Expected value between 1-59, got "%d"', $value));
        }

        parent::__construct($value);
    }
}

describe('IntegerSecond', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0-59', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->value())->toBe($value);
        })->with(range(0, 59));

        it('throws for values outside 0-59', function (int $invalidValue): void {
            expect(fn() => new IntegerSecond($invalidValue))
                ->toThrow(SecondIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1, 60, 100]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0-59', function (int $value): void {
            $second = IntegerSecond::fromInt($value);
            expect($second->value())->toBe($value);
        })->with(range(0, 59));

        it('throws for values outside 0-59', function (int $invalidValue): void {
            expect(fn() => IntegerSecond::fromInt($invalidValue))
                ->toThrow(SecondIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1, 60, 100]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0-59', function (string $value, int $expected): void {
            $second = IntegerSecond::fromString($value);
            expect($second->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['59', 59],
            ['45', 45],
        ]);

        it('throws SecondIntegerTypeException for values outside 0-59', function (string $invalidValue): void {
            expect(fn() => IntegerSecond::fromString($invalidValue))
                ->toThrow(SecondIntegerTypeException::class, 'Expected value between 0-59');
        })->with(['-1', '60', '100']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerSecond::fromString($invalidValue))
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
            $second = IntegerSecond::fromBool(true);
            expect($second->value())->toBe(1);
        });

        it('creates instance from false (0)', function (): void {
            $second = IntegerSecond::fromBool(false);
            expect($second->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0-59', function (float $value, int $expected): void {
            $second = IntegerSecond::fromFloat($value);
            expect($second->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [59.0, 59],
            [45.0, 45],
        ]);

        it('throws for float values outside 0-59', function (float $invalidValue): void {
            expect(fn() => IntegerSecond::fromFloat($invalidValue))
                ->toThrow(SecondIntegerTypeException::class, 'Expected value between 0-59');
        })->with([-1.0, 60.0, 100.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerSecond::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0-59', function (string $value, int $expected): void {
            $second = IntegerSecond::fromDecimal($value);
            expect($second->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['59.0', 59],
            ['45.0', 45],
        ]);

        it('throws for decimal values outside 0-59', function (string $invalidValue): void {
            expect(fn() => IntegerSecond::fromDecimal($invalidValue))
                ->toThrow(TypeException::class);
        })->with(['-1.0', '60.0', '100.0', '1.1', '58.1']);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerSecond::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['5', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerSecond for valid values 0-59', function (int $value): void {
            $result = IntegerSecond::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe($value);
        })->with(range(0, 59));

        it('returns Undefined for invalid values', function (int $invalidValue): void {
            $result = IntegerSecond::tryFromInt($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1, 60, 100]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerSecond for valid integer strings 0-59', function (string $value, int $expected): void {
            $result = IntegerSecond::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['59', 59],
        ]);

        it('returns Undefined for values outside 0-59', function (string $invalidValue): void {
            $result = IntegerSecond::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1', '60', '100']);

        it('returns Undefined for non-integer strings', function (string $invalidValue): void {
            $result = IntegerSecond::tryFromString($invalidValue);
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
        it('returns IntegerSecond from true', function (): void {
            $result = IntegerSecond::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerSecond from false', function (): void {
            $result = IntegerSecond::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe(0);
        });

        it('returns Undefined via subclass when bool produces out-of-range value', function (): void {
            $result = IntegerSecondTest::tryFromBool(false);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerSecond from float with exact integer value 0-59', function (float $value, int $expected): void {
            $result = IntegerSecond::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [59.0, 59],
            [45.0, 45],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerSecond::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([-1.0, 60.0, 3.14, 100.0]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerSecond from decimal string with exact integer value 0-59', function (string $value, int $expected): void {
            $result = IntegerSecond::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['59.0', 59],
            ['45.0', 45],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerSecond::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['-1.0', '60.0', '1.1', '58.1', '5', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerSecond for valid integer inputs 0-59', function (mixed $value, int $expected): void {
            $result = IntegerSecond::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [0, 0],
            [59, 59],
            ['45', 45],
            [true, 1],
            [false, 0],
            [4.0, 4],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerSecond::tryFromMixed($invalidValue);
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
            $second = new IntegerSecond($value);
            expect($second->toInt())->toBe($value);
        })->with([0, 59, 45]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $second = new IntegerSecond($value);
            expect($second->toString())->toBe($expected)
                ->and((string) $second)->toBe($expected);
        })->with([
            [0, '0'],
            [59, '59'],
            [45, '45'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->toFloat())->toBe((float) $value)
                ->and($second->toFloat())->toBeFloat();
        })->with([0, 59, 45]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $second = new IntegerSecond($value);
            expect($second->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
            [59, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $second = new IntegerSecond($value);
            expect($second->toDecimal())->toBe($expected);
        })->with([
            [0, '0.0'],
            [59, '59.0'],
            [45, '45.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->jsonSerialize())->toBe($value)
                ->and($second->jsonSerialize())->toBeInt();
        })->with([0, 59, 45]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->isEmpty())->toBeFalse();
        })->with([0, 59, 45]);

        it('isUndefined always returns false', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->isUndefined())->toBeFalse();
        })->with([0, 59, 45]);

        it('isTypeOf returns true for matching class', function (): void {
            $second = IntegerSecond::fromInt(5);
            expect($second->isTypeOf(IntegerSecond::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $second = IntegerSecond::fromInt(5);
            expect($second->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('isTypeOf returns true when at least one class matches', function (): void {
            $second = IntegerSecond::fromInt(5);
            expect($second->isTypeOf('NonExistentClass', IntegerSecond::class, 'AnotherClass'))->toBeTrue();
        });

        it('value() returns integer value', function (int $value): void {
            $second = new IntegerSecond($value);
            expect($second->value())->toBe($value);
        })->with([0, 59, 45]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerSecond::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerSecond::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 59, 45]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerSecond::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerSecond::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['0', '59', '45']);
    });

    // ============================================
    // EDGE CASES & COMPREHENSIVE TESTS
    // ============================================

    describe('Edge cases and comprehensive tests', function (): void {
        it('handles multiple round-trips for all valid values', function (int $original): void {
            $result = IntegerSecond::fromString(
                IntegerSecond::fromInt(
                    IntegerSecond::fromString(
                        IntegerSecond::fromInt($original)->toString()
                    )->toInt()
                )->toString()
            )->value();

            expect($result)->toBe($original);
        })->with([0, 59, 45]);

        it('handles Stringable objects', function (): void {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '45';
                }
            };

            $result = IntegerSecond::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerSecond::class)
                ->and($result->value())->toBe(45);
        });

        it('tryFromMixed catches TypeException for unserializable types', function (): void {
            $result = IntegerSecond::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    describe('Null checks', function () {
        it('throws exception on fromNull', function () {
            expect(fn() => IntegerSecond::fromNull(null))
                ->toThrow(SecondIntegerTypeException::class, 'Integer type cannot be created from null');
        });

        it('throws exception on toNull', function () {
            expect(fn() => IntegerSecond::toNull())
                ->toThrow(SecondIntegerTypeException::class, 'Integer type cannot be converted to null');
        });
    });
});
