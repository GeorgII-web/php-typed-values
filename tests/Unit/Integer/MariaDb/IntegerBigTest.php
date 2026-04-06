<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\MariaDb;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\MariaDb\IntegerBig;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(IntegerBig::class);

describe('IntegerBig', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->value())->toBe($value);
        })->with([PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values', function (int $value): void {
            $big = IntegerBig::fromInt($value);
            expect($big->value())->toBe($value);
        })->with([PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings', function (string $value, int $expected): void {
            $big = IntegerBig::fromString($value);
            expect($big->value())->toBe($expected);
        })->with([
            [(string) PHP_INT_MIN, PHP_INT_MIN],
            ['0', 0],
            [(string) PHP_INT_MAX, PHP_INT_MAX],
            ['-5', -5],
        ]);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerBig::fromString($invalidValue))
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
            $big = IntegerBig::fromBool(true);
            expect($big->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $big = IntegerBig::fromBool(false);
            expect($big->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value', function (float $value, int $expected): void {
            $big = IntegerBig::fromFloat($value);
            expect($big->value())->toBe($expected);
        })->with([
            [-9007199254740991.0, -9007199254740991],
            [0.0, 0],
            [9007199254740991.0, 9007199254740991],
            [5.0, 5],
        ]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerBig::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings', function (string $value, int $expected): void {
            $big = IntegerBig::fromDecimal($value);
            expect($big->value())->toBe($expected);
        })->with([
            ['-9007199254740991.0', -9007199254740991],
            ['0.0', 0],
            ['9007199254740991.0', 9007199254740991],
            ['5.0', 5],
        ]);

        it('throws for invalid decimal strings', function (string $invalidValue): void {
            expect(fn() => IntegerBig::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['42', 'abc', '']);
    });

    // ============================================
    // TRY-FROM METHODS (SAFE FACTORIES)
    // ============================================

    describe('tryFromInt method', function (): void {
        it('returns IntegerBig for valid values', function (int $value): void {
            $result = IntegerBig::tryFromInt($value);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe($value);
        })->with([PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX]);
    });

    describe('tryFromString method', function (): void {
        it('returns IntegerBig for valid integer strings', function (string $value, int $expected): void {
            $result = IntegerBig::tryFromString($value);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [(string) PHP_INT_MIN, PHP_INT_MIN],
            ['0', 0],
            [(string) PHP_INT_MAX, PHP_INT_MAX],
        ]);

        it('returns Undefined for invalid strings', function (string $invalidValue): void {
            $result = IntegerBig::tryFromString($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
            '12.3',
            '5.5',
            'a',
            '',
            '3.0',
            '01',
            '+1',
            ' 1',
            '1 ',
        ]);
    });

    describe('tryFromBool method', function (): void {
        it('returns IntegerBig from true', function (): void {
            $result = IntegerBig::tryFromBool(true);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe(1);
        });

        it('returns IntegerBig from false', function (): void {
            $result = IntegerBig::tryFromBool(false);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe(0);
        });
    });

    describe('tryFromFloat method', function (): void {
        it('returns IntegerBig from float with exact integer value', function (
            float $value,
            int $expected,
        ): void {
            $result = IntegerBig::tryFromFloat($value);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-9007199254740991.0, -9007199254740991],
            [0.0, 0],
            [9007199254740991.0, 9007199254740991],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid floats', function (float $invalidValue): void {
            $result = IntegerBig::tryFromFloat($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([3.14]);
    });

    describe('tryFromDecimal method', function (): void {
        it('returns IntegerBig from decimal string with exact integer value', function (string $value, int $expected): void {
            $result = IntegerBig::tryFromDecimal($value);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe($expected);
        })->with([
            ['-9007199254740991.0', -9007199254740991],
            ['0.0', 0],
            ['9007199254740991.0', 9007199254740991],
            ['5.0', 5],
        ]);

        it('returns Undefined for invalid decimal strings', function (string $invalidValue): void {
            $result = IntegerBig::tryFromDecimal($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with(['42', 'abc']);
    });

    describe('tryFromMixed method', function (): void {
        it('returns IntegerBig for valid integer inputs', function (mixed $value, int $expected): void {
            $result = IntegerBig::tryFromMixed($value);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe($expected);
        })->with([
            [-1, -1],
            [PHP_INT_MAX, PHP_INT_MAX],
            ['-5', -5],
            ['0', 0],
            [true, 1],
            [false, 0],
            [5.0, 5],
        ]);

        it('returns Undefined for invalid inputs', function (mixed $invalidValue): void {
            $result = IntegerBig::tryFromMixed($invalidValue);
            expect($result)->toBeInstanceOf(Undefined::class);
        })->with([
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
            $big = new IntegerBig($value);
            expect($big->toInt())->toBe($value);
        })->with([PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX]);

        it('toString returns string representation', function (int $value, string $expected): void {
            $big = new IntegerBig($value);
            expect($big->toString())->toBe($expected)
                ->and((string) $big)->toBe($expected);
        })->with([
            [PHP_INT_MIN, (string) PHP_INT_MIN],
            [0, '0'],
            [PHP_INT_MAX, (string) PHP_INT_MAX],
            [-5, '-5'],
        ]);

        it('toFloat returns float representation', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->toFloat())->toBe((float) $value);
        })->with([-9007199254740991, -1, 0, 1, 9007199254740991]);

        it('toBool returns correct boolean value', function (int $value, bool $expected): void {
            $big = new IntegerBig($value);
            expect($big->toBool())->toBe($expected);
        })->with([
            [0, false],
            [1, true],
        ]);

        it('toDecimal returns decimal string representation', function (int $value, string $expected): void {
            $big = new IntegerBig($value);
            expect($big->toDecimal())->toBe($expected);
        })->with([
            [-9007199254740991, '-9007199254740991.0'],
            [0, '0.0'],
            [9007199254740991, '9007199254740991.0'],
        ]);

        it('jsonSerialize returns integer value', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->jsonSerialize())->toBe($value);
        })->with([PHP_INT_MIN, -1, 0, 1, PHP_INT_MAX]);
    });

    // ============================================
    // TYPE CHECKS & PROPERTIES
    // ============================================

    describe('Type checks and properties', function (): void {
        it('isEmpty always returns false', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->isEmpty())->toBeFalse();
        })->with([PHP_INT_MIN, 0, PHP_INT_MAX]);

        it('isUndefined always returns false', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->isUndefined())->toBeFalse();
        })->with([PHP_INT_MIN, 0, PHP_INT_MAX]);

        it('isTypeOf returns true for matching class', function (): void {
            $big = IntegerBig::fromInt(5);
            expect($big->isTypeOf(IntegerBig::class))->toBeTrue();
        });

        it('isTypeOf returns false for non-matching class', function (): void {
            $big = IntegerBig::fromInt(5);
            expect($big->isTypeOf('NonExistentClass'))->toBeFalse();
        });

        it('value() returns integer value', function (int $value): void {
            $big = new IntegerBig($value);
            expect($big->value())->toBe($value);
        })->with([PHP_INT_MIN, 0, PHP_INT_MAX]);
    });

    // ============================================
    // ROUND-TRIP CONVERSIONS
    // ============================================

    describe('Round-trip conversions', function (): void {
        it('preserves value through int → string → int conversion', function (int $original): void {
            $v1 = IntegerBig::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerBig::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([PHP_INT_MIN, -50, 0, 50, PHP_INT_MAX]);

        it('preserves value through string → int → string conversion', function (string $original): void {
            $v1 = IntegerBig::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerBig::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with([(string) PHP_INT_MIN, '-50', '0', '50', (string) PHP_INT_MAX]);
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

            $result = IntegerBig::tryFromMixed($stringable);
            expect($result)->toBeInstanceOf(IntegerBig::class)
                ->and($result->value())->toBe(42);
        });

        it('tryFromMixed catches Exception for unserializable types', function (): void {
            $result = IntegerBig::tryFromMixed([]);
            expect($result)->toBeInstanceOf(Undefined::class);
        });
    });

    /**
     * @internal
     *
     * @coversNothing
     */
    readonly class IntegerBigTest extends IntegerBig
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

    describe('IntegerBig catch block coverage', function (): void {
        it('IntegerBig::tryFromBool catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBig::tryFromDecimal catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBig::tryFromFloat catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromFloat(1.0))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBig::tryFromInt catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromInt(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBig::tryFromMixed catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromMixed(1))->toBeInstanceOf(Undefined::class);
        });

        it('IntegerBig::tryFromString catch block coverage', function (): void {
            expect(IntegerBigTest::tryFromString('1'))->toBeInstanceOf(Undefined::class);
        });
    });
});
