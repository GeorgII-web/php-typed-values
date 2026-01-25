<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('FloatPositive', function () {
    describe('Creation', function () {
        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatPositive::tryFromString($input);
                if ($expected instanceof FloatPositive) {
                    expect($result)->toBeInstanceOf(FloatPositive::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid positive' => ['0.10000000000000001', FloatPositive::fromFloat(0.1)],
                'zero' => ['0.0', Undefined::create()],
                'negative' => ['-0.10000000000000001', Undefined::create()],
                'non-numeric' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatPositive::tryFromString('0.0', Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });

        describe('fromString', function () {
            it('creates instance from valid positive string', function (string $input, float $expected) {
                expect(FloatPositive::fromString($input)->value())->toBe($expected);
            })->with([
                'standard positive' => ['2.5', 2.5],
                'another positive' => ['1.25', 1.25],
            ]);

            it('throws exception on invalid string', function (string $input, string $exception, string $message) {
                expect(fn() => FloatPositive::fromString($input))->toThrow($exception, $message);
            })->with([
                'zero' => ['0.0', FloatTypeException::class, 'Expected positive float, got "0"'],
                'negative' => ['-1.23', StringTypeException::class, 'String "-1.23" has no valid strict float value'],
                'non-numeric' => ['unknown', StringTypeException::class, 'String "unknown" has no valid float value'],
            ]);
        });

        describe('tryFromFloat', function () {
            it('returns instance or default value', function (float $input, mixed $expected) {
                $result = FloatPositive::tryFromFloat($input);
                if ($expected instanceof FloatPositive) {
                    expect($result)->toBeInstanceOf(FloatPositive::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'positive' => [2.0, FloatPositive::fromFloat(2.0)],
                'zero' => [0.0, Undefined::create()],
            ]);
        });

        describe('fromFloat', function () {
            it('creates instance from valid positive float', function (float $input) {
                expect(FloatPositive::fromFloat($input)->value())->toBe($input);
            })->with([
                'standard positive' => [1.5],
            ]);

            it('throws exception on invalid float', function (float $input, string $message) {
                expect(fn() => FloatPositive::fromFloat($input))->toThrow(FloatTypeException::class, $message);
            })->with([
                'zero' => [0.0, 'Expected positive float, got "0"'],
                'negative' => [-1.0, 'Expected positive float, got "-1"'],
            ]);
        });

        describe('tryFromInt', function () {
            it('returns instance or default value', function (int $input, mixed $expected) {
                $result = FloatPositive::tryFromInt($input);
                if ($expected instanceof FloatPositive) {
                    expect($result)->toBeInstanceOf(FloatPositive::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'positive int' => [5, FloatPositive::fromFloat(5.0)],
                'zero int' => [0, Undefined::create()],
            ]);
        });

        describe('tryFromBool', function () {
            it('returns instance or default value', function (bool $input, mixed $expected) {
                $result = FloatPositive::tryFromBool($input);
                if ($expected instanceof FloatPositive) {
                    expect($result)->toBeInstanceOf(FloatPositive::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'true' => [true, FloatPositive::fromFloat(1.0)],
                'false' => [false, Undefined::create()],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, float $expected) {
                $result = FloatPositive::tryFromMixed($input);
                expect($result)->toBeInstanceOf(FloatPositive::class)
                    ->and($result->value())->toBe($expected);
            })->with([
                'float 1.5' => [1.5, 1.5],
                'PHP_FLOAT_MAX' => [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                'long float' => [1.234567890123456789, 1.234567890123456789],
                '2/3' => [2 / 3, 2 / 3],
                'FloatPositive instance' => [FloatPositive::fromFloat(1.234567890123456789), 1.234567890123456789],
                'int 1' => [1, 1.0],
                'int 111' => [111, 111.0],
                'bool true' => [true, 1.0],
                'string 1.5' => ['1.5', 1.5],
                'string 0.5' => ['0.5', 0.5],
                'string 0.1' => ['0.10000000000000001', 0.1],
                'string 3.14' => ['3.14000000000000012', 3.14],
                'stringable object' => [
                    new class {
                        public function __toString(): string
                        {
                            return '2.5';
                        }
                    },
                    2.5,
                ],
            ]);

            it('returns Undefined for closure', function () {
                expect(FloatPositive::tryFromMixed(fn() => 1.5))
                    ->toBeInstanceOf(Undefined::class);
            });

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = FloatPositive::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [[]],
                'object' => [new stdClass()],
                'non-numeric string' => ['not-a-float'],
                'invalid format string' => ['1.2.3'],
                'octal-like string' => ['007'],
                'Callable array' => [['FloatPositive', 'fromInt']],
                'Resource' => [fopen('php://memory', 'r')],
                'Array of objects' => [[new stdClass()]],
                'INF' => [\INF],
                'NAN' => [\NAN],
                'Null byte string' => ["\0"],
                'zero float' => [0.0],
                'zero integer' => [0],
                'false boolean' => [false],
                'zero string' => ['0'],
                'negative float' => [-3.14],
                'negative integer' => [-42],
                'negative string' => ['-10.5'],
            ]);

            it('kills the bool decrement mutant in tryFromMixed', function () {
                expect(FloatPositive::tryFromMixed(true))->toBeInstanceOf(FloatPositive::class)
                    ->and(FloatPositive::tryFromMixed(true)->value())->toBe(1.0)
                    ->and(FloatPositive::tryFromMixed(false))
                    ->toBeInstanceOf(Undefined::class);
            });
        });

        describe('Constructor', function () {
            it('constructs positive float via constructor', function () {
                $v = new FloatPositive(0.1);
                expect($v->value())->toBe(0.1)
                    ->and($v->toString())->toBe('0.10000000000000001');
            });

            it('throws on non-positive values', function (float $input, string $message) {
                expect(fn() => new FloatPositive($input))
                    ->toThrow(FloatTypeException::class, $message);
            })->with([
                'zero' => [0.0, 'Expected positive float, got "0"'],
                'negative' => [-0.1, 'Expected positive float, got "-0.1"'],
                'very small negative' => [-1e-308, 'Expected positive float, got "-1.0E-308"'],
            ]);
        });
    });

    describe('Instance Methods', function () {
        it('value() returns the internal float value', function () {
            expect(FloatPositive::fromFloat(1.5)->value())->toBe(1.5);
        });

        describe('toString and __toString', function () {
            it('returns string representation', function (float $value, string $expected) {
                $f = FloatPositive::fromFloat($value);
                expect($f->toString())->toBe($expected)
                    ->and((string) $f)->toBe($expected);
            })->with([
                '3.14' => [3.14, '3.14000000000000012'],
                '1.0' => [1.0, '1.0'],
            ]);
        });

        it('jsonSerialize() returns float', function () {
            expect(FloatPositive::tryFromString('1.10000000000000009')->jsonSerialize())->toBeFloat();
        });

        it('isEmpty() returns false', function () {
            expect(FloatPositive::fromFloat(0.1)->isEmpty())->toBeFalse();
        });

        it('isUndefined() returns false for instances and true for Undefined results', function () {
            $v1 = new FloatPositive(0.1);
            $u1 = FloatPositive::tryFromString('0.0');
            $u2 = FloatPositive::tryFromMixed('abc');
            $u3 = FloatPositive::tryFromFloat(0.0);

            expect($v1->isUndefined())->toBeFalse()
                ->and($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue()
                ->and($u3->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $v = FloatPositive::fromFloat(1.5);
                expect($v->isTypeOf(FloatPositive::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $v = FloatPositive::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $v = FloatPositive::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass', FloatPositive::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });

    describe('Conversions', function () {
        it('converts to bool', function (float $value, bool $expected) {
            expect(FloatPositive::fromFloat($value)->toBool())->toBe($expected);
        })->with([
            '1.0 to true' => [1.0, true],
        ]);

        it('throws when converting non-integer-like float to bool', function () {
            expect(fn() => FloatPositive::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class);
        });

        it('converts to int', function (float $value, int $expected) {
            expect(FloatPositive::fromFloat($value)->toInt())->toBe($expected);
        })->with([
            '1.0 to 1' => [1.0, 1],
        ]);

        it('throws when converting non-integer-like float to int', function () {
            expect(fn() => FloatPositive::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);
        });

        it('converts to float', function () {
            expect(FloatPositive::fromFloat(1.0)->toFloat())->toBe(1.0);
        });
    });
});
