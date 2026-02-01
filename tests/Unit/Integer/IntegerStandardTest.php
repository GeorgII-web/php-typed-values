<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerStandard', function () {
    describe('Factories', function () {
        it('creates from bool', function (bool $input, int $expected) {
            expect(IntegerStandard::fromBool($input)->value())->toBe($expected);
        })->with([
            'true' => [true, 1],
            'false' => [false, 0],
        ]);

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerStandard::fromFloat($input)->value())->toBe($expected);
        })->with([
            'positive' => [1.0, 1],
            'negative' => [-42.0, -42],
            'zero' => [0.0, 0],
        ]);

        it('throws when creating from invalid float', function (float $input) {
            IntegerStandard::fromFloat($input);
        })->throws(FloatTypeException::class)->with([
            'with precision' => [1.5],
            'INF' => [\INF],
            'NAN' => [\NAN],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerStandard::fromInt($input)->value())->toBe($input);
        })->with([
            'positive' => [42],
            'negative' => [-42],
            'zero' => [0],
            'max' => [\PHP_INT_MAX],
            'min' => [\PHP_INT_MIN],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerStandard::fromString($input)->value())->toBe($expected);
        })->with([
            'positive' => ['42', 42],
            'negative' => ['-42', -42],
            'zero' => ['0', 0],
            'max' => [(string) \PHP_INT_MAX, \PHP_INT_MAX],
            'min' => [(string) \PHP_INT_MIN, \PHP_INT_MIN],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerStandard::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'positive' => ['42.0', 42],
            'negative' => ['-42.0', -42],
            'zero' => ['0.0', 0],
        ]);

        it('throws when creating from invalid decimal string', function (string $input) {
            IntegerStandard::fromDecimal($input);
        })->throws(DecimalTypeException::class)->with([
            'not a decimal' => ['42'],
            'leading zero' => ['042.0'],
            'plus sign' => ['+42.0'],
            'empty' => [''],
            'whitespace' => [' 42.0 '],
            'text' => ['abc'],
            'scientific' => ['1e2.0'],
        ]);

        it('throws when creating from invalid string', function (string $input) {
            IntegerStandard::fromString($input);
        })->throws(StringTypeException::class)->with([
            'float string' => ['42.0'],
            'leading zero' => ['042'],
            'plus sign' => ['+42'],
            'empty' => [''],
            'whitespace' => [' 42 '],
            'text' => ['abc'],
            'scientific' => ['1e2'],
            'hex' => ['0x1A'],
            'octal' => ['012'],
            'float dot start' => ['.42'],
            'float dot end' => ['42.'],
        ]);
    });

    describe('Try Factories', function () {
        it('tryFromBool returns instance or default', function (bool $input) {
            $result = IntegerStandard::tryFromBool($input);
            expect($result)->toBeInstanceOf(IntegerStandard::class)
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
            readonly class IntegerStandardTest extends IntegerStandard
            {
                public static function fromBool(bool $value): static
                {
                    throw new Exception('forced failure');
                }
            }

            expect(IntegerStandardTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerStandard::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerStandard::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid' => [1.0, false],
            'invalid' => [1.5, true],
        ]);

        it('tryFromInt always returns instance', function (int $input) {
            expect(IntegerStandard::tryFromInt($input))->toBeInstanceOf(IntegerStandard::class)
                ->and(IntegerStandard::tryFromInt($input)->value())->toBe($input);
        })->with([42, -42, 0]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerStandard::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerStandard::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid' => ['123', false],
            'invalid' => ['12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerStandard::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerStandard::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid' => ['123.0', false],
            'invalid' => ['123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            $result = IntegerStandard::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerStandard::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'int' => [42, 42],
            'float' => [42.0, 42],
            'bool' => [true, 1],
            'string' => ['42', 42],
            'instance' => [IntegerStandard::fromInt(42), 42],
            'stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            }, 42],
        ]);

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerStandard::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'invalid float' => [1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool', function (int $input, bool $expected) {
            expect((new IntegerStandard($input))->toBool())->toBe($expected);
        })->with([
            'zero' => [0, false],
            'positive' => [1, true],
            'negative' => [-1, true],
        ]);

        it('converts to float', function (int $input) {
            $v = new IntegerStandard($input);
            expect($v->toFloat())->toBe((float) $input);
        })->with([
            'zero' => [0],
            'positive' => [42],
            'negative' => [-42],
        ]);

        it('throws when toFloat loses precision', function () {
            // PHP floats have 53 bits of mantissa. 2^53 + 1 cannot be represented exactly.
            $val = 9007199254740993; // 2^53 + 1
            if ($val !== (int) (float) $val) {
                expect(fn() => (new IntegerStandard($val))->toFloat())->toThrow(IntegerTypeException::class);
            } else {
                // Try PHP_INT_MAX if 2^53+1 didn't work (though it should on 64bit)
                $val = \PHP_INT_MAX;
                if ($val !== (int) (float) $val) {
                    expect(fn() => (new IntegerStandard($val))->toFloat())->toThrow(IntegerTypeException::class);
                }
            }
        });

        it('converts to int', function (int $input) {
            expect((new IntegerStandard($input))->toInt())->toBe($input);
        })->with([42, -42, 0]);

        it('converts to string', function (int $input) {
            expect((new IntegerStandard($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerStandard($input)))->toBe((string) $input);
        })->with([42, -42, 0]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerStandard($input))->toDecimal())->toBe($expected);
        })->with([
            'positive' => [42, '42.0'],
            'negative' => [-42, '-42.0'],
            'zero' => [0, '0.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerStandard($input))->jsonSerialize())->toBe($input);
        })->with([42, -42, 0]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerStandard(0))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerStandard(0))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerStandard(42);
            expect($v->isTypeOf(IntegerStandard::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerStandard::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });
});
