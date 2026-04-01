<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\AgeIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\Specific\IntegerAge;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class IntegerAgeTest extends IntegerAge
{
    public function __construct(int $value)
    {
        throw new Exception('Forced exception for coverage');
    }
}

covers(IntegerAge::class);

describe('IntegerAge', function (): void {
    // ============================================
    // CONSTRUCTOR & FACTORY METHODS
    // ============================================

    describe('Constructor', function (): void {
        it('creates instance for valid values 0-150', function (int $value): void {
            $age = new IntegerAge($value);
            expect($age->value())->toBe($value);
        })->with([0, 1, 75, 149, 150]);

        it('throws for values outside 0-150', function (int $invalidValue): void {
            expect(fn() => new IntegerAge($invalidValue))
                ->toThrow(AgeIntegerTypeException::class, 'Expected value between 0-150');
        })->with([-1, 151, -100, 200]);
    });

    describe('fromInt factory', function (): void {
        it('creates instance for valid values 0-150', function (int $value): void {
            $age = IntegerAge::fromInt($value);
            expect($age->value())->toBe($value);
        })->with([0, 1, 75, 149, 150]);

        it('throws for values outside 0-150', function (int $invalidValue): void {
            expect(fn() => IntegerAge::fromInt($invalidValue))
                ->toThrow(AgeIntegerTypeException::class, 'Expected value between 0-150');
        })->with([-1, 151, -100, 200]);
    });

    describe('fromString factory', function (): void {
        it('creates instance for valid integer strings 0-150', function (string $value, int $expected): void {
            $age = IntegerAge::fromString($value);
            expect($age->value())->toBe($expected);
        })->with([
            ['0', 0],
            ['1', 1],
            ['150', 150],
        ]);

        it('throws AgeIntegerTypeException for values outside 0-150', function (string $invalidValue): void {
            expect(fn() => IntegerAge::fromString($invalidValue))
                ->toThrow(AgeIntegerTypeException::class, 'Expected value between 0-150');
        })->with(['-1', '151']);

        it('throws for non-integer strings', function (string $invalidValue, string $exceptionClass): void {
            expect(fn() => IntegerAge::fromString($invalidValue))
                ->toThrow($exceptionClass);
        })->with([
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
            $age = IntegerAge::fromBool(true);
            expect($age->value())->toBe(1);
        });

        it('creates instance from false', function (): void {
            $age = IntegerAge::fromBool(false);
            expect($age->value())->toBe(0);
        });
    });

    describe('fromFloat factory', function (): void {
        it('creates instance from float with exact integer value 0-150', function (float $value, int $expected): void {
            $age = IntegerAge::fromFloat($value);
            expect($age->value())->toBe($expected);
        })->with([
            [0.0, 0],
            [1.0, 1],
            [150.0, 150],
        ]);

        it('throws for float values outside 0-150', function (float $invalidValue): void {
            expect(fn() => IntegerAge::fromFloat($invalidValue))
                ->toThrow(AgeIntegerTypeException::class, 'Expected value between 0-150');
        })->with([-1.0, 151.0]);

        it('throws FloatTypeException for non-integer floats', function (): void {
            expect(fn() => IntegerAge::fromFloat(3.14))
                ->toThrow(FloatTypeException::class);
        });
    });

    describe('fromDecimal factory', function (): void {
        it('creates instance from valid decimal strings 0-150', function (string $value, int $expected): void {
            $age = IntegerAge::fromDecimal($value);
            expect($age->value())->toBe($expected);
        })->with([
            ['0.0', 0],
            ['1.0', 1],
            ['150.0', 150],
        ]);

        it('throws for decimal values outside 0-150', function (string $invalidValue): void {
            expect(fn() => IntegerAge::fromDecimal($invalidValue))
                ->toThrow(AgeIntegerTypeException::class, 'Expected value between 0-150');
        })->with(['-1.0', '151.0']);

        it('throws for decimal values without valid int value', function (string $invalidValue): void {
            expect(fn() => IntegerAge::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['-0.1', '150.1']);

        it('throws DecimalTypeException for non-integer decimals', function (string $invalidValue): void {
            expect(fn() => IntegerAge::fromDecimal($invalidValue))
                ->toThrow(DecimalTypeException::class);
        })->with(['1.5', '0', '150']);
    });

    // ============================================
    // TRY-FACTORY METHODS
    // ============================================

    describe('tryFromInt factory', function (): void {
        it('returns instance for valid value', function (): void {
            $age = IntegerAge::tryFromInt(25);
            expect($age)->toBeInstanceOf(IntegerAge::class)
                ->and($age->toInt())->toBe(25);
        });

        it('returns default for invalid value', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromInt(200, $default);
            expect($result)->toBe($default);
        });
    });

    describe('tryFromString factory', function (): void {
        it('returns instance for valid string', function (): void {
            $age = IntegerAge::tryFromString('25');
            expect($age)->toBeInstanceOf(IntegerAge::class)
                ->and($age->toInt())->toBe(25);
        });

        it('returns default for invalid string', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromString('abc', $default);
            expect($result)->toBe($default);
        });
    });

    describe('tryFromBool factory', function (): void {
        it('returns instance for bool', function (bool $val, int $expected): void {
            $age = IntegerAge::tryFromBool($val);
            expect($age->value())->toBe($expected);
        })->with([
            [true, 1],
            [false, 0],
        ]);

        it('returns default on exception in subclass', function (): void {
            $default = new Undefined();
            $result = IntegerAgeTest::tryFromBool(true, $default);
            expect($result)->toBe($default);
        });
    });

    describe('tryFromFloat factory', function (): void {
        it('returns instance for valid float', function (): void {
            $age = IntegerAge::tryFromFloat(25.0);
            expect($age->value())->toBe(25);
        });

        it('returns default for invalid float', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromFloat(25.5, $default);
            expect($result)->toBe($default);
        });
    });

    describe('tryFromDecimal factory', function (): void {
        it('returns instance for valid decimal string', function (): void {
            $age = IntegerAge::tryFromDecimal('25.0');
            expect($age)->toBeInstanceOf(IntegerAge::class)
                ->and($age->value())->toBe(25);
        });

        it('returns default for invalid decimal string', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromDecimal('25.5', $default);
            expect($result->isUndefined())->toBeTrue()
                ->and($result)->toBe($default);
        });
    });

    describe('tryFromMixed factory', function (): void {
        it('returns instance for various valid types', function (mixed $val, int $expected): void {
            $age = IntegerAge::tryFromMixed($val);
            expect($age->value())->toBe($expected);
        })->with([
            [25, 25],
            ['25', 25],
            [25.0, 25],
            [true, 1],
            [new class implements \Stringable {
                public function __toString(): string
                {
                    return '25';
                }
            }, 25],
        ]);

        it('returns default for invalid mixed value', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromMixed(['not', 'an', 'age'], $default);
            expect($result)->toBe($default);
        });

        it('returns default for invalid Stringable value', function (): void {
            $default = new Undefined();
            $val = new class implements \Stringable {
                public function __toString(): string
                {
                    return 'not-an-age';
                }
            };
            $result = IntegerAge::tryFromMixed($val, $default);
            expect($result)->toBe($default);
        });

        it('returns default for non-stringable object', function (): void {
            $default = new Undefined();
            $val = new \stdClass();
            $result = IntegerAge::tryFromMixed($val, $default);
            expect($result)->toBe($default);
        });

        it('returns default for out-of-range value', function (): void {
            $default = new Undefined();
            $result = IntegerAge::tryFromMixed(200, $default);
            expect($result)->toBe($default);
        });
    });

    // ============================================
    // CONVERSION METHODS
    // ============================================

    describe('Conversion and state methods', function (): void {
        it('correctly converts to other types', function (): void {
            $age = new IntegerAge(25);
            expect($age->toInt())->toBe(25)
                ->and($age->value())->toBe(25)
                ->and($age->toString())->toBe('25')
                ->and((string) $age)->toBe('25')
                ->and($age->toFloat())->toBe(25.0)
                ->and($age->toBool())->toBe(true)
                ->and($age->toDecimal())->toBe('25.0')
                ->and($age->jsonSerialize())->toBe(25);
        });

        it('returns false for isEmpty and isUndefined', function (): void {
            $age = new IntegerAge(25);
            expect($age->isEmpty())->toBeFalse()
                ->and($age->isUndefined())->toBeFalse();
        });

        it('correctly identifies type with isTypeOf', function (): void {
            $age = new IntegerAge(25);
            expect($age->isTypeOf(IntegerAge::class))->toBeTrue()
                ->and($age->isTypeOf('NonExistentClass'))->toBeFalse()
                ->and($age->isTypeOf('NonExistentClass', IntegerAge::class))->toBeTrue();
        });
    });
});
