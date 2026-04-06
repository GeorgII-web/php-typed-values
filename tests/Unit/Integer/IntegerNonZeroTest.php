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
use PhpTypedValues\Exception\Integer\NonZeroIntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\IntegerNonZero;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

use function gettype;

covers(IntegerNonZero::class);

describe('IntegerNonZero', function () {
    describe('Factories', function () {
        it('creates from bool', function (bool $input, int $expected) {
            expect(IntegerNonZero::fromBool($input)->value())->toBe($expected);
        })->with([
            'true' => [true, 1],
        ]);

        it('throws when creating from false bool', function () {
            expect(fn() => IntegerNonZero::fromBool(false))->toThrow(NonZeroIntegerTypeException::class);
        });

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerNonZero::fromFloat($input)->value())->toBe($expected);
        })->with([
            'positive' => [42.0, 42],
            'negative' => [-42.0, -42],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception) {
            expect(fn() => IntegerNonZero::fromFloat($input))->toThrow($exception);
        })->with([
            'zero' => [0.0, NonZeroIntegerTypeException::class],
            'with precision' => [1.5, FloatTypeException::class],
            'INF' => [INF, FloatTypeException::class],
            'NAN' => [NAN, FloatTypeException::class],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerNonZero::fromInt($input)->value())->toBe($input);
        })->with([
            'positive' => [42],
            'negative' => [-42],
            'max' => [PHP_INT_MAX],
            'min' => [PHP_INT_MIN],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerNonZero::fromInt($input))->toThrow(NonZeroIntegerTypeException::class);
        })->with([
            'zero' => [0],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerNonZero::fromString($input)->value())->toBe($expected);
        })->with([
            'positive' => ['42', 42],
            'negative' => ['-42', -42],
            'max' => [(string) PHP_INT_MAX, PHP_INT_MAX],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerNonZero::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'positive' => ['42.0', 42],
            'negative' => ['-42.0', -42],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerNonZero::fromDecimal($input))->toThrow($exception);
        })->with([
            'zero' => ['0.0', NonZeroIntegerTypeException::class],
            'not a decimal' => ['42', DecimalTypeException::class],
            'leading zero' => ['042.0', DecimalTypeException::class],
            'plus sign' => ['+42.0', DecimalTypeException::class],
            'empty' => ['', DecimalTypeException::class],
            'whitespace' => [' 42.0 ', DecimalTypeException::class],
            'text' => ['abc', DecimalTypeException::class],
            'scientific' => ['1e2.0', DecimalTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerNonZero::fromString($input))->toThrow($exception);
        })->with([
            'zero' => ['0', NonZeroIntegerTypeException::class],
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
            $result = IntegerNonZero::tryFromBool($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonZero::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'true' => [true, false],
            'false' => [false, true],
        ]);

        it('tryFromBool returns default when fromBool throws', function () {
            /**
             * @internal
             *
             * @coversNothing
             */
            readonly class IntegerNonZeroTest extends IntegerNonZero
            {
                public static function fromBool(bool $value): static
                {
                    throw new Exception('forced failure');
                }
            }

            expect(IntegerNonZeroTest::tryFromBool(true))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerNonZero::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonZero::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'invalid zero' => [0.0, true],
            'valid positive' => [1.0, false],
            'valid negative' => [-1.0, false],
            'invalid precision' => [1.5, true],
        ]);

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerNonZero::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonZero::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'invalid zero' => [0, true],
            'positive' => [42, false],
            'negative' => [-1, false],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonZero::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonZero::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'invalid zero' => ['0', true],
            'valid positive' => ['123', false],
            'valid negative' => ['-1', false],
            'invalid' => ['12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNonZero::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNonZero::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'invalid zero' => ['0.0', true],
            'valid positive' => ['123.0', false],
            'valid negative' => ['-1.0', false],
            'invalid' => ['123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            $result = IntegerNonZero::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerNonZero::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'int' => [42, 42],
            'negative int' => [-42, -42],
            'float' => [42.0, 42],
            'bool' => [true, 1],
            'string' => ['42', 42],
            'instance' => [IntegerNonZero::fromInt(42), 42],
            'stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '42';
                }
            }, 42],
        ]);

        it('tryFromMixed handles Stringable and raw strings correctly to kill mutants', function () {
            expect(IntegerNonZero::tryFromMixed('123'))->toBeInstanceOf(IntegerNonZero::class);

            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '456';
                }
            };
            expect(IntegerNonZero::tryFromMixed($stringable))->toBeInstanceOf(IntegerNonZero::class);

            expect(IntegerNonZero::tryFromMixed(null))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed uses string cast for Stringable', function () {
            $stringable = new class implements Stringable {
                public function __toString(): string
                {
                    return '789';
                }
            };
            expect(IntegerNonZero::tryFromMixed($stringable)->value())->toBe(789);
        });

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerNonZero::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'zero int' => [0],
            'zero float' => [0.0],
            'invalid float' => [1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool and ensures explicit cast', function (int $input, bool $expected) {
            $v = new IntegerNonZero($input);
            expect($v->toBool())->toBe($expected)
                ->and(gettype($v->toBool()))->toBe('boolean');
        })->with([
            'positive' => [1, true],
            'negative' => [-1, true],
        ]);

        it('converts to float and ensures precision checks', function (int $input) {
            $v = new IntegerNonZero($input);
            $float = $v->toFloat();
            expect($float)->toBe((float) $input)
                ->and(gettype($float))->toBe('double');

            expect($float)->not->toBe($input); // strict comparison between float and int
        })->with([
            'positive' => [42],
            'negative' => [-42],
        ]);

        it('throws when toFloat loses precision', function () {
            $val = 9007199254740993; // 2^53 + 1
            if ($val !== (int) (float) $val) {
                expect(fn() => (new IntegerNonZero($val))->toFloat())->toThrow(IntegerTypeException::class);
            }

            $safeVal = 9007199254740992; // 2^53
            expect(fn() => (new IntegerNonZero($safeVal))->toFloat())->not->toThrow(IntegerTypeException::class);
        });

        it('converts to int', function (int $input) {
            expect((new IntegerNonZero($input))->toInt())->toBe($input);
        })->with([42, -42]);

        it('converts to string', function (int $input) {
            expect((new IntegerNonZero($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerNonZero($input)))->toBe((string) $input);
        })->with([42, -42]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerNonZero($input))->toDecimal())->toBe($expected);
        })->with([
            'positive' => [42, '42.0'],
            'negative' => [-42, '-42.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerNonZero($input))->jsonSerialize())->toBe($input);
        })->with([42, -42]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerNonZero(42))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerNonZero(42))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerNonZero(42);
            expect($v->isTypeOf(IntegerNonZero::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerNonZero::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });

    describe('Round-trip conversions', function () {
        it('preserves value through int → string → int conversion', function (int $original) {
            $v1 = IntegerNonZero::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNonZero::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([42, -42, PHP_INT_MAX, PHP_INT_MIN]);

        it('preserves value through string → int → string conversion', function (string $original) {
            $v1 = IntegerNonZero::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerNonZero::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['42', '-42', (string) PHP_INT_MAX]);
    });
});
