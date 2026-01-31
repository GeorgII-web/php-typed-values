<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerNonNegative', function () {
    describe('Factories', function () {
        it('creates from bool', function (bool $input, int $expected) {
            expect(IntegerNonNegative::fromBool($input)->value())->toBe($expected);
        })->with([
            'true' => [true, 1],
            'false' => [false, 0],
        ]);

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerNonNegative::fromFloat($input)->value())->toBe($expected);
        })->with([
            'zero' => [0.0, 0],
            'positive' => [42.0, 42],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception) {
            expect(fn() => IntegerNonNegative::fromFloat($input))->toThrow($exception);
        })->with([
            'negative' => [-1.0, IntegerTypeException::class],
            'with precision' => [1.5, FloatTypeException::class],
            'INF' => [\INF, FloatTypeException::class],
            'NAN' => [\NAN, FloatTypeException::class],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerNonNegative::fromInt($input)->value())->toBe($input);
        })->with([
            'zero' => [0],
            'positive' => [42],
            'max' => [\PHP_INT_MAX],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerNonNegative::fromInt($input))->toThrow(IntegerTypeException::class);
        })->with([
            'negative' => [-1],
            'min' => [\PHP_INT_MIN],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerNonNegative::fromString($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0', 0],
            'positive' => ['42', 42],
            'max' => [(string) \PHP_INT_MAX, \PHP_INT_MAX],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerNonNegative::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0.0', 0],
            'positive' => ['42.0', 42],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerNonNegative::fromDecimal($input))->toThrow($exception);
        })->with([
            'negative' => ['-1.0', IntegerTypeException::class],
            'not a decimal' => ['42', StringTypeException::class],
            'leading zero' => ['042.0', StringTypeException::class],
            'plus sign' => ['+42.0', StringTypeException::class],
            'empty' => ['', StringTypeException::class],
            'whitespace' => [' 42.0 ', StringTypeException::class],
            'text' => ['abc', StringTypeException::class],
            'scientific' => ['1e2.0', StringTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerNonNegative::fromString($input))->toThrow($exception);
        })->with([
            'negative' => ['-1', IntegerTypeException::class],
            'float string' => ['42.0', StringTypeException::class],
            'leading zero' => ['042', StringTypeException::class],
            'plus sign' => ['+42', StringTypeException::class],
            'empty' => ['', StringTypeException::class],
            'whitespace' => [' 42 ', StringTypeException::class],
            'text' => ['abc', StringTypeException::class],
            'scientific' => ['1e2', StringTypeException::class],
        ]);
    });

    describe('Try Factories', function () {
        it('tryFromBool returns instance or default', function (bool $input) {
            $result = IntegerNonNegative::tryFromBool($input);
            expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                ->and($result->value())->toBe((int) $input);
        })->with([
            'true' => [true],
            'false' => [false],
        ]);

        it('tryFromBool returns default when fromBool throws', function () {
            /**
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerNonNegativeTest extends IntegerNonNegative
            {
                public static function fromBool(bool $value): static
                {
                    throw new Exception('forced failure');
                }
            }

            expect(IntegerNonNegativeTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerNonNegative::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid zero' => [0.0, false],
            'valid positive' => [1.0, false],
            'negative' => [-1.0, true],
            'invalid precision' => [1.5, true],
        ]);

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerNonNegative::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'zero' => [0, false],
            'positive' => [42, false],
            'negative' => [-1, true],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonNegative::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid zero' => ['0', false],
            'valid positive' => ['123', false],
            'negative' => ['-1', true],
            'invalid' => ['12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonNegative::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid zero' => ['0.0', false],
            'valid positive' => ['123.0', false],
            'negative' => ['-1.0', true],
            'invalid' => ['123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            $result = IntegerNonNegative::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerNonNegative::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'int' => [42, 42],
            'zero int' => [0, 0],
            'float' => [42.0, 42],
            'bool' => [true, 1],
            'false bool' => [false, 0],
            'string' => ['42', 42],
            'instance' => [IntegerNonNegative::fromInt(42), 42],
            'stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            }, 42],
        ]);

        it('tryFromMixed handles Stringable and raw strings correctly to kill mutants', function () {
            // Kill InstanceOfToTrue/False and BooleanOrToBooleanAnd
            // Case 1: is_string is true, Stringable is false
            expect(IntegerNonNegative::tryFromMixed('123'))->toBeInstanceOf(IntegerNonNegative::class);

            // Case 2: is_string is false, Stringable is true
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '456';
                }
            };
            expect(IntegerNonNegative::tryFromMixed($stringable))->toBeInstanceOf(IntegerNonNegative::class);

            // Case 3: Both false (already covered by other tests, but for clarity)
            expect(IntegerNonNegative::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed uses string cast for Stringable', function () {
            // Kill RemoveStringCast
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '789';
                }
            };
            // If (string) cast is removed, it might pass $stringable object to fromString which expects string
            expect(IntegerNonNegative::tryFromMixed($stringable)->value())->toBe(789);
        });

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerNonNegative::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'negative int' => [-1],
            'negative float' => [-1.0],
            'invalid float' => [1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool and ensures explicit cast', function (int $input, bool $expected) {
            $v = new IntegerNonNegative($input);
            expect($v->toBool())->toBe($expected)
                ->and(\gettype($v->toBool()))->toBe('boolean');
        })->with([
            'zero' => [0, false],
            'positive' => [1, true],
        ]);

        it('converts to float and ensures precision checks', function (int $input) {
            $v = new IntegerNonNegative($input);
            $float = $v->toFloat();
            expect($float)->toBe((float) $input)
                ->and(\gettype($float))->toBe('double');

            // Explicitly verify the value for mutants like RemoveDoubleCast
            // If (float) cast is removed, it might return int (though return type hint would catch it,
            // but the mutant might bypass or change behavior if logic is complex)
            if ($input !== 0) {
                expect($float)->not->toBe($input); // strict comparison between float and int
            }
        })->with([
            'zero' => [0],
            'positive' => [42],
        ]);

        it('throws when toFloat loses precision', function () {
            // PHP floats have 53 bits of mantissa. 2^53 + 1 cannot be represented exactly.
            $val = 9007199254740993; // 2^53 + 1
            if ($val !== (int) (float) $val) {
                // If the condition is negated (IfNegated) or NotIdenticalToIdentical,
                // this will either not throw when it should, or throw when it shouldn't.
                expect(fn() => (new IntegerNonNegative($val))->toFloat())->toThrow(IntegerTypeException::class);
            }

            // Case where it should NOT throw (exact representation)
            $safeVal = 9007199254740992; // 2^53
            expect(fn() => (new IntegerNonNegative($safeVal))->toFloat())->not->toThrow(IntegerTypeException::class);
        });

        it('converts to int', function (int $input) {
            expect((new IntegerNonNegative($input))->toInt())->toBe($input);
        })->with([0, 42]);

        it('converts to string', function (int $input) {
            expect((new IntegerNonNegative($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerNonNegative($input)))->toBe((string) $input);
        })->with([0, 42]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerNonNegative($input))->toDecimal())->toBe($expected);
        })->with([
            'zero' => [0, '0.0'],
            'positive' => [42, '42.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerNonNegative($input))->jsonSerialize())->toBe($input);
        })->with([0, 42]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerNonNegative(0))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerNonNegative(0))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerNonNegative(42);
            expect($v->isTypeOf(IntegerNonNegative::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerNonNegative::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });

    describe('Round-trip conversions', function () {
        it('preserves value through int → string → int conversion', function (int $original) {
            $v1 = IntegerNonNegative::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNonNegative::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, 42, \PHP_INT_MAX]);

        it('preserves value through string → int → string conversion', function (string $original) {
            $v1 = IntegerNonNegative::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerNonNegative::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['0', '42', (string) \PHP_INT_MAX]);
    });
});
