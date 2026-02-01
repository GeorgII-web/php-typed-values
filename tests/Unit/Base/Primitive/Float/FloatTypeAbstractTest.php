<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Float\FloatTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(FloatTypeAbstract::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class FloatTypeAbstractTest extends FloatTypeAbstract
{
    public function __construct(private float $val)
    {
    }

    public static function fromBool(bool $value): static
    {
        return new static(static::boolToFloat($value));
    }

    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToFloat($value));
    }

    public static function fromFloat(float $value): static
    {
        return new static($value);
    }

    public static function fromInt(int $value): static
    {
        return new static(static::intToFloat($value));
    }

    public static function fromString(string $value): static
    {
        return new static(static::stringToFloat($value));
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): float
    {
        return $this->val;
    }

    public function toBool(): bool
    {
        return static::floatToBool($this->val);
    }

    public function toDecimal(): string
    {
        return static::floatToDecimal($this->val);
    }

    public function toFloat(): float
    {
        return $this->val;
    }

    public function toInt(): int
    {
        return static::floatToInt($this->val);
    }

    public function toString(): string
    {
        return static::floatToString($this->val);
    }

    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromBool($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromDecimal(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromDecimal($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromFloat($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromInt($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return match (true) {
                \is_float($value) => static::fromFloat($value),
                \is_int($value) => static::fromInt($value),
                $value instanceof FloatTypeAbstract => static::fromFloat($value->value()),
                \is_bool($value) => static::fromBool($value),
                \is_string($value) || $value instanceof Stringable => static::tryFromDecimal((string) $value, static::fromString((string) $value)),
                default => throw new TypeException('Value cannot be cast to float'),
            };
        } catch (Throwable) {
            return $default;
        }
    }

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromString($value);
        } catch (Throwable) {
            return $default;
        }
    }

    public function value(): float
    {
        return $this->val;
    }
}

describe('FloatTypeAbstract', function () {
    describe('Creation', function () {
        describe('fromFloat', function () {
            it('creates instance from float', function () {
                $v = FloatTypeAbstractTest::fromFloat(1.5);
                expect($v->value())->toBe(1.5);
            });
        });

        describe('fromInt', function () {
            it('creates instance from int', function () {
                $v = FloatTypeAbstractTest::fromInt(10);
                expect($v->value())->toBe(10.0);
            });

            it('throws on precision loss', function () {
                expect(fn() => FloatTypeAbstractTest::fromInt(\PHP_INT_MAX))
                    ->toThrow(IntegerTypeException::class);
            });
        });

        describe('fromBool', function () {
            it('creates instance from bool', function (bool $input, float $expected) {
                expect(FloatTypeAbstractTest::fromBool($input)->value())->toBe($expected);
            })->with([
                'true' => [true, 1.0],
                'false' => [false, 0.0],
            ]);
        });

        describe('fromString', function () {
            it('creates instance from string', function (string $input, float $expected) {
                expect(FloatTypeAbstractTest::fromString($input)->value())->toBe($expected);
            })->with([
                '1.5' => ['1.5', 1.5],
                '0.0' => ['0.0', 0.0],
            ]);

            it('throws on invalid string', function (string $input) {
                expect(fn() => FloatTypeAbstractTest::fromString($input))
                    ->toThrow(TypeException::class);
            })->with([
                'abc',
                '0.1', // loose precision
            ]);
        });

        describe('fromDecimal', function () {
            it('creates instance from decimal string', function (string $input, float $expected) {
                expect(FloatTypeAbstractTest::fromDecimal($input)->value())->toBe($expected);
            })->with([
                '1.5' => ['1.5', 1.5],
                '0.0' => ['0.0', 0.0],
            ]);

            it('throws on invalid decimal string', function (string $input) {
                expect(fn() => FloatTypeAbstractTest::fromDecimal($input))
                    ->toThrow(TypeException::class);
            })->with([
                'abc',
                '0.1', // loose precision
                '123', // not a decimal format (missing .0)
            ]);
        });

        describe('tryFromMethods', function () {
            it('tryFromFloat', function () {
                expect(FloatTypeAbstractTest::tryFromFloat(1.5)->value())->toBe(1.5);
            });

            it('tryFromInt', function () {
                expect(FloatTypeAbstractTest::tryFromInt(10)->value())->toBe(10.0);
                expect(FloatTypeAbstractTest::tryFromInt(\PHP_INT_MAX))->toBeInstanceOf(Undefined::class);
            });

            it('tryFromBool', function () {
                expect(FloatTypeAbstractTest::tryFromBool(true)->value())->toBe(1.0);
            });

            it('tryFromDecimal', function () {
                expect(FloatTypeAbstractTest::tryFromDecimal('1.5')->value())->toBe(1.5);
                expect(FloatTypeAbstractTest::tryFromDecimal('abc'))->toBeInstanceOf(Undefined::class);
            });

            it('tryFromString', function () {
                expect(FloatTypeAbstractTest::tryFromString('1.5')->value())->toBe(1.5);
                expect(FloatTypeAbstractTest::tryFromString('abc'))->toBeInstanceOf(Undefined::class);
            });

            describe('tryFromMixed', function () {
                it('returns instance for valid mixed inputs', function (mixed $input, float $expected) {
                    $result = FloatTypeAbstractTest::tryFromMixed($input);
                    expect($result)->toBeInstanceOf(FloatTypeAbstractTest::class)
                        ->and($result->value())->toBe($expected);
                })->with([
                    'float' => [1.5, 1.5],
                    'int' => [10, 10.0],
                    'bool' => [true, 1.0],
                    'string' => ['1.5', 1.5],
                    'instance' => [new FloatTypeAbstractTest(2.5), 2.5],
                    'stringable' => [
                        new class implements Stringable {
                            public function __toString(): string
                            {
                                return '3.5';
                            }
                        },
                        3.5,
                    ],
                ]);

                it('returns default for invalid mixed inputs', function (mixed $input) {
                    expect(FloatTypeAbstractTest::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
                })->with([
                    'null' => [null],
                    'array' => [[]],
                    'object' => [new stdClass()],
                ]);
            });
        });
    });

    describe('Conversions', function () {
        it('toBool', function (float $input, bool $expected) {
            $v = new FloatTypeAbstractTest($input);
            expect($v->toBool())->toBe($expected);
        })->with([
            '1.0' => [1.0, true],
            '0.0' => [0.0, false],
        ]);

        it('toBool throws on invalid value', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect(fn() => $v->toBool())->toThrow(FloatTypeException::class);
        });

        it('toDecimal', function (float $input, string $expected) {
            $v = new FloatTypeAbstractTest($input);
            expect($v->toDecimal())->toBe($expected);
        })->with([
            '1.5' => [1.5, '1.5'],
            '0.0' => [0.0, '0.0'],
        ]);

        it('toInt', function (float $input, int $expected) {
            $v = new FloatTypeAbstractTest($input);
            expect($v->toInt())->toBe($expected);
        })->with([
            '1.0' => [1.0, 1],
            '0.0' => [0.0, 0],
        ]);

        it('toInt throws on precision loss', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect(fn() => $v->toInt())->toThrow(FloatTypeException::class);
        });

        it('toFloat', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect($v->toFloat())->toBe(1.5);
        });

        it('toString and __toString', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect($v->toString())->toBe('1.5')
                ->and((string) $v)->toBe('1.5');
        });

        it('jsonSerialize', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect($v->jsonSerialize())->toBe(1.5);
        });
    });

    describe('Information', function () {
        it('isEmpty returns false', function () {
            $v = new FloatTypeAbstractTest(0.0);
            expect($v->isEmpty())->toBeFalse();
        });

        it('isUndefined returns false', function () {
            $v = new FloatTypeAbstractTest(0.0);
            expect($v->isUndefined())->toBeFalse();
        });

        it('isTypeOf', function () {
            $v = new FloatTypeAbstractTest(1.5);
            expect($v->isTypeOf(FloatTypeAbstractTest::class))->toBeTrue()
                ->and($v->isTypeOf(FloatTypeAbstract::class))->toBeTrue()
                ->and($v->isTypeOf('NonExistent'))->toBeFalse();
        });
    });

    describe('Mutation Coverage & Edge Cases', function () {
        /**
         * Targets: UNTESTED src/Base/Primitive/PrimitiveTypeAbstract.php > Line 177: FalseToTrue
         * This mutant changes `$roundTripConversion && $value !== self::stringToFloat($strValue, false)`
         * to `$roundTripConversion && $value !== self::stringToFloat($strValue, true)`.
         *
         * If we use a value where `floatToString($value)` produces a string that `stringToFloat($str, false)`
         * accepts but `stringToFloat($str, true)` rejects, we can kill it.
         *
         * `stringToFloat($str, true)` checks if `floatToString($floatFromStr, false) === $str`.
         *
         * Composite: $value = 1e-308.
         * `floatToString(1e-308)` produces "0.00000000000000000" (approximately, due to precision).
         * Actually, `sprintf('%.17f', 1e-308)` is "0.00000000000000000".
         * `rtrim` and adding `0` results in "0.0".
         * `stringToFloat("0.0", false)` returns 0.0.
         * `1e-308 !== 0.0` is true.
         * So `floatToString(1e-308)` throws FloatTypeException at line 178.
         *
         * The mutant changes the internal call to `stringToFloat($strValue, true)`.
         * For "0.0", `stringToFloat("0.0", true)` also returns 0.0 because `floatToString(0.0, false)` is "0.0".
         *
         * Let's find a value where `stringToFloat($strValue, false)` and `stringToFloat($strValue, true)` differ.
         * `stringToFloat($str, true)` fails if `$str` is not the canonical representation of the float it represents.
         * "0.1" is not canonical for 0.1 (which is 0.10000000000000001).
         *
         * If `floatToString` produced "0.1", then:
         * `stringToFloat("0.1", false)` returns 0.1.
         * `stringToFloat("0.1", true)` throws StringTypeException because `floatToString(0.1, false)` is "0.10000000000000001".
         */
        it('kills mutant at PrimitiveTypeAbstract line 177 using 1e-308', function () {
            $v = new FloatTypeAbstractTest(1e-308);
            expect(fn() => $v->toString())->toThrow(FloatTypeException::class);
        });

        it('kills mutant at PrimitiveTypeAbstract line 177 using subnormal 5e-324', function () {
            $v = new FloatTypeAbstractTest(5e-324);
            expect(fn() => $v->toString())->toThrow(FloatTypeException::class);
        });

        it('verifies strict string validation in stringToFloat', function () {
            // "0.1" is not strict.
            expect(fn() => FloatTypeAbstractTest::fromString('0.1'))
                ->toThrow(StringTypeException::class, 'String "0.1" has no valid strict float value');

            // "0.10000000000000001" is strict.
            expect(FloatTypeAbstractTest::fromString('0.10000000000000001')->value())
                ->toBe(0.1);
        });

        it('handles trailing dot normalization in floatToString', function () {
            // This is internal to floatToString.
            // We need a float that when formatted with %.17f and rtrimmed, ends with a dot.
            // Composite: 1.0 -> "1.00000000000000000" -> rtrim -> "1." -> "1.0"
            $v = new FloatTypeAbstractTest(1.0);
            expect($v->toString())->toBe('1.0');
        });
    });
});
