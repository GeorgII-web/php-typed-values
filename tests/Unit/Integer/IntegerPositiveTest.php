<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerPositive', function () {
    describe('Factories', function () {
        it('creates from bool', function () {
            expect(IntegerPositive::fromBool(true)->value())->toBe(1);
        });

        it('throws when creating from false bool', function () {
            IntegerPositive::fromBool(false);
        })->throws(IntegerTypeException::class, 'Expected positive integer, got "0"');

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerPositive::fromFloat($input)->value())->toBe($expected);
        })->with([
            'positive' => [1.0, 1],
            'large positive' => [42.0, 42],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception, string $message) {
            expect(fn() => IntegerPositive::fromFloat($input))->toThrow($exception, $message);
        })->with([
            'zero' => [0.0, IntegerTypeException::class, 'Expected positive integer, got "0"'],
            'negative' => [-1.0, IntegerTypeException::class, 'Expected positive integer, got "-1"'],
            'with precision' => [1.5, FloatTypeException::class, 'Float "1.5" has no valid strict int value'],
            'INF' => [\INF, FloatTypeException::class, 'Float "INF" has no valid strict int value'],
            'NAN' => [\NAN, FloatTypeException::class, 'Float "NAN" has no valid strict int value'],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerPositive::fromInt($input)->value())->toBe($input);
        })->with([
            'one' => [1],
            'positive' => [42],
            'max' => [\PHP_INT_MAX],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerPositive::fromInt($input))->toThrow(IntegerTypeException::class, "Expected positive integer, got \"{$input}\"");
        })->with([
            'zero' => [0],
            'negative' => [-1],
            'min' => [\PHP_INT_MIN],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerPositive::fromString($input)->value())->toBe($expected);
        })->with([
            'one' => ['1', 1],
            'positive' => ['42', 42],
            'max' => [(string) \PHP_INT_MAX, \PHP_INT_MAX],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerPositive::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'one' => ['1.0', 1],
            'positive' => ['42.0', 42],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerPositive::fromDecimal($input))->toThrow($exception);
        })->with([
            'zero' => ['0.0', TypeException::class],
            'negative' => ['-1.0', TypeException::class],
            'not a decimal' => ['42', DecimalTypeException::class],
            'leading zero' => ['042.0', DecimalTypeException::class],
            'plus sign' => ['+42.0', DecimalTypeException::class],
            'empty' => ['', DecimalTypeException::class],
            'whitespace' => [' 42.0 ', DecimalTypeException::class],
            'text' => ['abc', DecimalTypeException::class],
            'scientific' => ['1e2.0', DecimalTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerPositive::fromString($input))->toThrow($exception);
        })->with([
            'zero' => ['0', IntegerTypeException::class],
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
        it('tryFromBool returns instance or default', function (bool $input, bool $shouldFail) {
            $result = IntegerPositive::tryFromBool($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'true' => [true, false],
            'false' => [false, true],
        ]);

        it('tryFromBool returns default when fromBool throws exception other than IntegerTypeException', function () {
            /**
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerPositiveTest extends IntegerPositive
            {
                public static function fromBool(bool $value): static
                {
                    throw new Exception('forced failure');
                }
            }

            expect(IntegerPositiveTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerPositive::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid' => [1.0, false],
            'zero' => [0.0, true],
            'negative' => [-1.0, true],
            'invalid' => [1.5, true],
        ]);

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerPositive::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPositive::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'positive' => [42, false],
            'zero' => [0, true],
            'negative' => [-42, true],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerPositive::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid' => ['123', false],
            'zero' => ['0', true],
            'invalid' => ['12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerPositive::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPositive::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid' => ['123.0', false],
            'zero' => ['0.0', true],
            'invalid' => ['123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            $result = IntegerPositive::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerPositive::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'int' => [42, 42],
            'float' => [42.0, 42],
            'bool' => [true, 1],
            'string' => ['42', 42],
            'instance' => [IntegerPositive::fromInt(42), 42],
            'stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            }, 42],
        ]);

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerPositive::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'zero int' => [0],
            'negative int' => [-1],
            'zero float' => [0.0],
            'false bool' => [false],
            'invalid float' => [1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool', function () {
            expect((new IntegerPositive(1))->toBool())->toBeTrue();
        });

        it('converts to float', function (int $input) {
            $v = new IntegerPositive($input);
            expect($v->toFloat())->toBe((float) $input);
        })->with([
            'one' => [1],
            'positive' => [42],
        ]);

        it('throws when toFloat loses precision', function () {
            // PHP floats have 53 bits of mantissa. 2^53 + 1 cannot be represented exactly.
            $val = 9007199254740993; // 2^53 + 1
            if ($val !== (int) (float) $val) {
                expect(fn() => (new IntegerPositive($val))->toFloat())->toThrow(IntegerTypeException::class);
            } else {
                // Try PHP_INT_MAX if 2^53+1 didn't work (though it should on 64bit)
                $val = \PHP_INT_MAX;
                if ($val !== (int) (float) $val) {
                    expect(fn() => (new IntegerPositive($val))->toFloat())->toThrow(IntegerTypeException::class);
                }
            }
        });

        it('converts to int', function (int $input) {
            expect((new IntegerPositive($input))->toInt())->toBe($input);
        })->with([1, 42]);

        it('converts to string', function (int $input) {
            expect((new IntegerPositive($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerPositive($input)))->toBe((string) $input);
        })->with([1, 42]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerPositive($input))->toDecimal())->toBe($expected);
        })->with([
            'one' => [1, '1.0'],
            'positive' => [42, '42.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerPositive($input))->jsonSerialize())->toBe($input);
        })->with([1, 42]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerPositive(1))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerPositive(1))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerPositive(42);
            expect($v->isTypeOf(IntegerPositive::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerPositive::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });
});
