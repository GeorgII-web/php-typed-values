<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\IntegerNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerNegative', function () {
    describe('Factories', function () {
        it('creates from bool throws', function (bool $input) {
            expect(fn() => IntegerNegative::fromBool($input))->toThrow(IntegerTypeException::class);
        })->with([
            'true' => [true],
            'false' => [false],
        ]);

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerNegative::fromFloat($input)->value())->toBe($expected);
        })->with([
            'negative' => [-42.0, -42],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception) {
            expect(fn() => IntegerNegative::fromFloat($input))->toThrow($exception);
        })->with([
            'zero' => [0.0, IntegerTypeException::class],
            'positive' => [42.0, IntegerTypeException::class],
            'with precision' => [-1.5, FloatTypeException::class],
            'INF' => [\INF, FloatTypeException::class],
            'NAN' => [\NAN, FloatTypeException::class],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerNegative::fromInt($input)->value())->toBe($input);
        })->with([
            'negative' => [-42],
            'min' => [\PHP_INT_MIN],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerNegative::fromInt($input))->toThrow(IntegerTypeException::class);
        })->with([
            'zero' => [0],
            'positive' => [1],
            'max' => [\PHP_INT_MAX],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerNegative::fromString($input)->value())->toBe($expected);
        })->with([
            'negative' => ['-42', -42],
            'min' => [(string) \PHP_INT_MIN, \PHP_INT_MIN],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerNegative::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'negative' => ['-42.0', -42],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerNegative::fromDecimal($input))->toThrow($exception);
        })->with([
            'zero' => ['0.0', IntegerTypeException::class],
            'positive' => ['42.0', IntegerTypeException::class],
            'not a decimal' => ['-42', DecimalTypeException::class],
            'leading zero' => ['-042.0', DecimalTypeException::class],
            'empty' => ['', DecimalTypeException::class],
            'whitespace' => [' -42.0 ', DecimalTypeException::class],
            'text' => ['abc', DecimalTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerNegative::fromString($input))->toThrow($exception);
        })->with([
            'zero' => ['0', IntegerTypeException::class],
            'positive' => ['1', IntegerTypeException::class],
            'float string' => ['-42.0', StringTypeException::class],
            'leading zero' => ['-042', StringTypeException::class],
            'empty' => ['', StringTypeException::class],
            'text' => ['abc', StringTypeException::class],
        ]);
    });

    describe('Try Factories', function () {
        it('tryFromBool returns Undefined', function (bool $input) {
            expect(IntegerNegative::tryFromBool($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'true' => [true],
            'false' => [false],
        ]);

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerNegative::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNegative::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid negative' => [-1.0, false],
            'zero' => [0.0, true],
            'positive' => [1.0, true],
            'invalid precision' => [-1.5, true],
        ]);

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerNegative::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNegative::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'negative' => [-42, false],
            'zero' => [0, true],
            'positive' => [42, true],
        ]);

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNegative::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNegative::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid negative' => ['-123', false],
            'zero' => ['0', true],
            'positive' => ['123', true],
            'invalid' => ['-12.3', true],
        ]);

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerNegative::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerNegative::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid negative' => ['-123.0', false],
            'zero' => ['0.0', true],
            'positive' => ['123.0', true],
            'invalid' => ['-123', true],
        ]);

        it('tryFromMixed returns instance for valid inputs', function (mixed $input, int $expected) {
            //            var_dump('________'.$input . ' | ' . $expected);
            $result = IntegerNegative::tryFromMixed($input);
            expect($result)->toBeInstanceOf(IntegerNegative::class)
                ->and($result->value())->toBe($expected);
        })->with([
            'negative int' => [-42, -42],
            'negative float' => [-42.0, -42],
            'negative string' => ['-42', -42],
            'negative decimal string' => ['-42.0', -42],
            'instance' => [IntegerNegative::fromInt(-42), -42],
            'stringable negative' => [new class implements Stringable {
                public function __toString(): string
                {
                    return '-42';
                }
            }, -42],
        ]);

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerNegative::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'zero int' => [0],
            'positive int' => [1],
            'zero float' => [0.0],
            'positive float' => [1.0],
            'bool true' => [true],
            'bool false' => [false],
            'invalid string' => ['abc'],
            'invalid stringable' => [new class implements Stringable {
                public function __toString(): string
                {
                    return 'abc';
                }
            }],
            'object' => [new stdClass()],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool', function (int $input, bool $expected) {
            $v = new IntegerNegative($input);
            expect($v->toBool())->toBe($expected);
        })->with([
            'negative' => [-1, true],
        ]);

        it('converts to float and ensures precision checks', function (int $input) {
            $v = new IntegerNegative($input);
            $float = $v->toFloat();
            expect($float)->toBe((float) $input)
                ->and(\gettype($float))->toBe('double');
        })->with([
            'negative' => [-42],
        ]);

        it('throws when toFloat loses precision', function () {
            $val = -9007199254740993; // -(2^53 + 1)
            if ($val !== (int) (float) $val) {
                expect(fn() => new IntegerNegative($val)->toFloat())->toThrow(IntegerTypeException::class);
            }

            $safeVal = -9007199254740992; // -(2^53)
            expect(fn() => new IntegerNegative($safeVal)->toFloat())->not->toThrow(IntegerTypeException::class);
        });

        it('converts to int', function (int $input) {
            expect(new IntegerNegative($input)->toInt())->toBe($input);
        })->with([-42]);

        it('converts to string', function (int $input) {
            expect(new IntegerNegative($input)->toString())->toBe((string) $input)
                ->and((string) (new IntegerNegative($input)))->toBe((string) $input);
        })->with([-42]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect(new IntegerNegative($input)->toDecimal())->toBe($expected);
        })->with([
            'negative' => [-42, '-42.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect(new IntegerNegative($input)->jsonSerialize())->toBe($input);
        })->with([-42]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect(new IntegerNegative(-1)->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect(new IntegerNegative(-1)->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerNegative(-42);
            expect($v->isTypeOf(IntegerNegative::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerNegative::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });
    });

    describe('Round-trip conversions', function () {
        it('preserves value through int → string → int conversion', function (int $original) {
            $v1 = IntegerNegative::fromInt($original);
            $str = $v1->toString();
            $v2 = IntegerNegative::fromString($str);
            expect($v2->value())->toBe($original);
        })->with([-1, -42, \PHP_INT_MIN]);

        it('preserves value through string → int → string conversion', function (string $original) {
            $v1 = IntegerNegative::fromString($original);
            $int = $v1->toInt();
            $v2 = IntegerNegative::fromInt($int);
            expect($v2->toString())->toBe($original);
        })->with(['-1', '-42', (string) \PHP_INT_MIN]);
    });
});
