<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\Bool\BoolTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(BoolTypeAbstract::class);

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolTypeAbstractTest extends BoolTypeAbstract
{
    public function __construct(private bool $val)
    {
    }

    public static function fromBool(bool $value): static
    {
        return new static($value);
    }

    public static function fromDecimal(string $value): static
    {
        return new static(static::decimalToBool($value));
    }

    public static function fromFloat(float $value): static
    {
        return new static(static::floatToBool($value));
    }

    public static function fromInt(int $value): static
    {
        return new static(static::intToBool($value));
    }

    public static function fromString(string $value): static
    {
        return new static(static::stringToBool($value));
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

    public function jsonSerialize(): bool
    {
        return $this->val;
    }

    public function toBool(): bool
    {
        return $this->val;
    }

    public function toDecimal(): string
    {
        return static::boolToDecimal($this->val);
    }

    public function toFloat(): float
    {
        return static::boolToFloat($this->val);
    }

    public function toInt(): int
    {
        return static::boolToInt($this->val);
    }

    public function toString(): string
    {
        return static::boolToString($this->val);
    }

    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        return static::fromBool($value);
    }

    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromFloat($value);
        } catch (Exception) {
            return $default;
        }
    }

    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromInt($value);
        } catch (Exception) {
            return $default;
        }
    }

    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return match (true) {
                \is_bool($value) => static::fromBool($value),
                \is_float($value) => static::fromFloat($value),
                \is_int($value) => static::fromInt($value),
                \is_string($value) || $value instanceof Stringable => static::fromString((string) $value),
                default => throw new TypeException('Value cannot be cast to bool'),
            };
        } catch (Exception) {
            return $default;
        }
    }

    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            return static::fromString($value);
        } catch (Exception) {
            return $default;
        }
    }

    public function value(): bool
    {
        return $this->val;
    }
}

describe('BoolTypeAbstract', function () {
    describe('Creation', function () {
        describe('fromBool', function () {
            it('creates instance from bool', function (bool $input) {
                expect(BoolTypeAbstractTest::fromBool($input)->value())->toBe($input);
            })->with([
                'true' => [true],
                'false' => [false],
            ]);
        });

        describe('fromFloat', function () {
            it('creates instance from valid float', function (float $input, bool $expected) {
                expect(BoolTypeAbstractTest::fromFloat($input)->value())->toBe($expected);
            })->with([
                '1.0' => [1.0, true],
                '0.0' => [0.0, false],
            ]);

            it('throws on invalid float', function (float $input) {
                expect(fn() => BoolTypeAbstractTest::fromFloat($input))
                    ->toThrow(FloatTypeException::class);
            })->with([
                '0.5' => [0.5],
                'NAN' => [\NAN],
                'INF' => [\INF],
            ]);
        });

        describe('fromInt', function () {
            it('creates instance from valid int', function (int $input, bool $expected) {
                expect(BoolTypeAbstractTest::fromInt($input)->value())->toBe($expected);
            })->with([
                '1' => [1, true],
                '0' => [0, false],
            ]);

            it('throws on invalid int', function (int $input) {
                expect(fn() => BoolTypeAbstractTest::fromInt($input))
                    ->toThrow(IntegerTypeException::class);
            })->with([
                '2' => [2],
                '-1' => [-1],
            ]);
        });

        describe('fromString', function () {
            it('creates instance from valid string', function (string $input, bool $expected) {
                expect(BoolTypeAbstractTest::fromString($input)->value())->toBe($expected);
            })->with([
                'true' => ['true', true],
                'false' => ['false', false],
            ]);

            it('throws on invalid string', function (string $input) {
                expect(fn() => BoolTypeAbstractTest::fromString($input))
                    ->toThrow(StringTypeException::class);
            })->with([
                '1' => ['1'],
                'yes' => ['yes'],
                '' => [''],
            ]);
        });

        describe('tryFromMethods', function () {
            it('tryFromBool', function () {
                expect(BoolTypeAbstractTest::tryFromBool(true)->value())->toBeTrue();
            });

            it('tryFromFloat', function () {
                expect(BoolTypeAbstractTest::tryFromFloat(1.0)->value())->toBeTrue();
                expect(BoolTypeAbstractTest::tryFromFloat(0.5))->toBeInstanceOf(Undefined::class);
            });

            it('tryFromInt', function () {
                expect(BoolTypeAbstractTest::tryFromInt(1)->value())->toBeTrue();
                expect(BoolTypeAbstractTest::tryFromInt(2))->toBeInstanceOf(Undefined::class);
            });

            it('tryFromString', function () {
                expect(BoolTypeAbstractTest::tryFromString('true')->value())->toBeTrue();
                expect(BoolTypeAbstractTest::tryFromString('abc'))->toBeInstanceOf(Undefined::class);
            });

            describe('tryFromMixed', function () {
                it('returns instance for valid mixed inputs', function (mixed $input, bool $expected) {
                    $result = BoolTypeAbstractTest::tryFromMixed($input);
                    expect($result)->toBeInstanceOf(BoolTypeAbstractTest::class)
                        ->and($result->value())->toBe($expected);
                })->with([
                    'bool true' => [true, true],
                    'bool false' => [false, false],
                    'float 1.0' => [1.0, true],
                    'int 0' => [0, false],
                    'string true' => ['true', true],
                    'Stringable object' => [
                        new class implements Stringable {
                            public function __toString(): string
                            {
                                return 'false';
                            }
                        },
                        false,
                    ],
                ]);

                it('returns default for invalid mixed inputs', function (mixed $input) {
                    expect(BoolTypeAbstractTest::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
                })->with([
                    'null' => [null],
                    'array' => [[]],
                    'stdClass' => [new stdClass()],
                    'invalid string' => ['not-a-bool'],
                ]);
            });
        });
    });

    describe('Conversions', function () {
        it('toFloat', function (bool $input, float $expected) {
            expect(BoolTypeAbstractTest::fromBool($input)->toFloat())->toBe($expected);
        })->with([
            'true' => [true, 1.0],
            'false' => [false, 0.0],
        ]);

        it('toInt', function (bool $input, int $expected) {
            expect(BoolTypeAbstractTest::fromBool($input)->toInt())->toBe($expected);
        })->with([
            'true' => [true, 1],
            'false' => [false, 0],
        ]);

        it('toString and __toString', function (bool $input, string $expected) {
            $v = BoolTypeAbstractTest::fromBool($input);
            expect($v->toString())->toBe($expected)
                ->and((string) $v)->toBe($expected);
        })->with([
            'true' => [true, 'true'],
            'false' => [false, 'false'],
        ]);

        it('jsonSerialize', function () {
            expect(BoolTypeAbstractTest::fromBool(true)->jsonSerialize())->toBeTrue();
        });

        it('toBool', function () {
            expect(BoolTypeAbstractTest::fromBool(true)->toBool())->toBeTrue();
        });
    });

    describe('Information', function () {
        it('value', function () {
            expect(BoolTypeAbstractTest::fromBool(true)->value())->toBeTrue();
        });

        it('isEmpty returns false', function () {
            expect(BoolTypeAbstractTest::fromBool(true)->isEmpty())->toBeFalse();
        });

        it('isUndefined returns false', function () {
            expect(BoolTypeAbstractTest::fromBool(true)->isUndefined())->toBeFalse();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $v = BoolTypeAbstractTest::fromBool(true);
                expect($v->isTypeOf(BoolTypeAbstractTest::class))->toBeTrue()
                    ->and($v->isTypeOf(BoolTypeAbstract::class))->toBeTrue()
                    ->and($v->isTypeOf(PrimitiveTypeAbstract::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $v = BoolTypeAbstractTest::fromBool(true);
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $v = BoolTypeAbstractTest::fromBool(true);
                expect($v->isTypeOf('NonExistentClass', BoolTypeAbstract::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });
});
