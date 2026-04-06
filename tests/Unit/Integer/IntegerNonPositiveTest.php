<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Integer;

use const INF;
use const NAN;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\Integer\NonPositiveIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\IntegerNonPositive;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

use function gettype;

covers(IntegerNonPositive::class);

describe('IntegerNonPositive', function () {
    describe('Factories', function () {
        it('creates from bool', function (bool $input, int $expected) {
            expect(IntegerNonPositive::fromBool($input)->value())->toBe($expected);
        })->with([
            'false' => [false, 0],
        ]);

        it('throws when creating from true bool', function () {
            expect(fn() => IntegerNonPositive::fromBool(true))->toThrow(NonPositiveIntegerTypeException::class);
        });

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerNonPositive::fromFloat($input)->value())->toBe($expected);
        })->with([
            'zero' => [0.0, 0],
            'negative' => [-42.0, -42],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception) {
            expect(fn() => IntegerNonPositive::fromFloat($input))->toThrow($exception);
        })->with([
            'positive' => [42.0, NonPositiveIntegerTypeException::class],
            'with precision' => [1.5, FloatTypeException::class],
            'INF' => [INF, FloatTypeException::class],
            'NAN' => [NAN, FloatTypeException::class],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerNonPositive::fromInt($input)->value())->toBe($input);
        })->with([
            'zero' => [0],
            'negative' => [-42],
            'min' => [PHP_INT_MIN],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerNonPositive::fromInt($input))->toThrow(NonPositiveIntegerTypeException::class);
        })->with([
            'positive' => [42],
            'max' => [PHP_INT_MAX],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerNonPositive::fromString($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0', 0],
            'negative' => ['-42', -42],
            'min' => [(string) PHP_INT_MIN, PHP_INT_MIN],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerNonPositive::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0.0', 0],
            'negative' => ['-42.0', -42],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerNonPositive::fromDecimal($input))->toThrow($exception);
        })->with([
            'positive' => ['42.0', TypeException::class],
            'not a decimal' => ['-42', DecimalTypeException::class],
            'leading zero' => ['-042.0', DecimalTypeException::class],
            'plus sign' => ['+42.0', DecimalTypeException::class],
            'empty' => ['', DecimalTypeException::class],
            'whitespace' => [' -42.0 ', DecimalTypeException::class],
            'text' => ['abc', DecimalTypeException::class],
            'scientific' => ['-1e2.0', DecimalTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerNonPositive::fromString($input))->toThrow($exception);
        })->with([
            'positive' => ['42', NonPositiveIntegerTypeException::class],
            'float string' => ['-42.0', StringTypeException::class],
            'leading zero' => ['-042', StringTypeException::class],
            'plus sign' => ['+42', StringTypeException::class],
            'empty' => ['', StringTypeException::class],
            'whitespace' => [' -42 ', StringTypeException::class],
            'text' => ['abc', StringTypeException::class],
            'scientific' => ['-1e2', StringTypeException::class],
        ]);
    });

    describe('Try Factories', function () {
        it('tryFromBool returns instance or default', function (bool $input, bool $shouldFail) {
            $result = IntegerNonPositive::tryFromBool($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'true' => [true, true],
            'false' => [false, false],
        ]);

        it('tryFromBool returns default when fromBool throws', function () {
            /**
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerNonPositiveTest extends IntegerNonPositive
            {
                public static function fromBool(bool $value): static
                {
                    throw new Exception('forced failure');
                }
            }

            expect(IntegerNonPositiveTest::tryFromBool(false))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerNonPositive::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid zero' => [0.0, false],
            'invalid positive' => [1.0, true],
            'valid negative' => [-1.0, false],
            'invalid precision' => [-1.5, true],
        ]);

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerNonPositive::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'valid zero' => [0, false],
            'invalid positive' => [42, true],
            'valid negative' => [-1, false],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonPositive::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid zero' => ['0', false],
            'invalid positive' => ['123', true],
            'valid negative' => ['-1', false],
            'invalid' => ['-12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonPositive::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid zero' => ['0.0', false],
            'invalid positive' => ['123.0', true],
            'valid negative' => ['-1.0', false],
            'invalid' => ['-123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            $result = IntegerNonPositive::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerNonPositive::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'int' => [0, 0],
            'negative int' => [-42, -42],
            'float' => [-42.0, -42],
            'bool' => [false, 0],
            'string' => ['-42', -42],
            'instance' => [IntegerNonPositive::fromInt(-42), -42],
            'stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '-42';
                }
            }, -42],
        ]);

        it('tryFromMixed handles Stringable and raw strings correctly to kill mutants', function () {
            expect(IntegerNonPositive::tryFromMixed('-123'))->toBeInstanceOf(IntegerNonPositive::class);

            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '-456';
                }
            };
            expect(IntegerNonPositive::tryFromMixed($stringable))->toBeInstanceOf(IntegerNonPositive::class);

            expect(IntegerNonPositive::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed uses string cast for Stringable', function () {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '-789';
                }
            };
            expect(IntegerNonPositive::tryFromMixed($stringable)->value())->toBe(-789);
        });

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerNonPositive::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'positive int' => [42],
            'positive float' => [42.0],
            'invalid float' => [-1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool and ensures explicit cast', function (int $input, bool $expected) {
            $v = new IntegerNonPositive($input);
            expect($v->toBool())->toBe($expected)
                ->and(gettype($v->toBool()))->toBe('boolean');
        })->with([
            'zero' => [0, false],
            'negative' => [-1, true],
        ]);

        it('converts to float and ensures precision checks', function (int $input) {
            $v = new IntegerNonPositive($input);
            $float = $v->toFloat();
            expect($float)->toBe((float) $input)
                ->and(gettype($float))->toBe('double');

            if ($input !== 0) {
                expect($float)->not->toBe($input); // strict comparison between float and int
            }
        })->with([
            'zero' => [0],
            'negative' => [-42],
        ]);

        it('throws when toFloat loses precision', function () {
            $val = -9007199254740993; // -2^53 - 1
            if ($val !== (int) (float) $val) {
                expect(fn() => (new IntegerNonPositive($val))->toFloat())->toThrow(IntegerTypeException::class);
            }

            $safeVal = -9007199254740992; // -2^53
            expect(fn() => (new IntegerNonPositive($safeVal))->toFloat())->not->toThrow(IntegerTypeException::class);
        });

        it('converts to int', function (int $input) {
            expect((new IntegerNonPositive($input))->toInt())->toBe($input);
        })->with([0, -42]);

        it('converts to string', function (int $input) {
            expect((new IntegerNonPositive($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerNonPositive($input)))->toBe((string) $input);
        })->with([0, -42]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerNonPositive($input))->toDecimal())->toBe($expected);
        })->with([
            'zero' => [0, '0.0'],
            'negative' => [-42, '-42.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerNonPositive($input))->jsonSerialize())->toBe($input);
        })->with([0, -42]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerNonPositive(0))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerNonPositive(0))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerNonPositive(-42);
            expect($v->isTypeOf(IntegerNonPositive::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerNonPositive::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });

    describe('Round-trip conversions', function () {
        it('preserves value through int → string → int conversion', function (int $original) {
            $v1 = IntegerNonPositive::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNonPositive::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([0, -42, PHP_INT_MIN]);

        it('preserves value through string → int → string conversion', function (string $original) {
            $v1 = IntegerNonPositive::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerNonPositive::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['0', '-42', (string) PHP_INT_MIN]);
    });
});
