<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('FloatNonNegative', function () {
    describe('Creation', function () {
        describe('fromDecimal', function () {
            it('creates instance from valid decimal string', function (string $input, float $expected) {
                expect(FloatNonNegative::fromDecimal($input)->value())->toBe($expected);
            })->with([
                'positive decimal' => ['1.5', 1.5],
                'zero decimal' => ['0.0', 0.0],
            ]);

            it('throws exception on invalid decimal string', function (string $input, string $exception) {
                expect(fn() => FloatNonNegative::fromDecimal($input))->toThrow($exception);
            })->with([
                'non-numeric' => ['abc', DecimalTypeException::class],
                'integer format' => ['123', StringTypeException::class],
                'negative decimal' => ['-1.5', TypeException::class],
            ]);
        });

        describe('tryFromDecimal', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatNonNegative::tryFromDecimal($input);
                if ($expected instanceof FloatNonNegative) {
                    expect($result)->toBeInstanceOf(FloatNonNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid decimal' => ['1.5', FloatNonNegative::fromFloat(1.5)],
                'negative decimal' => ['-1.5', Undefined::create()],
                'invalid decimal' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatNonNegative::tryFromDecimal('abc', Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });
        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatNonNegative::tryFromString($input);
                if ($expected instanceof FloatNonNegative) {
                    expect($result)->toBeInstanceOf(FloatNonNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid positive' => ['0.5', FloatNonNegative::fromFloat(0.5)],
                'zero' => ['0.0', FloatNonNegative::fromFloat(0.0)],
                'negative' => ['-0.10000000000000001', Undefined::create()],
                'non-numeric' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatNonNegative::tryFromString('abc', Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });

        describe('fromString', function () {
            it('creates instance from valid non-negative string', function (string $input, float $expected) {
                expect(FloatNonNegative::fromString($input)->value())->toBe($expected);
            })->with([
                'standard positive' => ['2.5', 2.5],
                'zero' => ['0.0', 0.0],
                'scientific representation' => ['3.14000000000000012', 3.14],
                'integer-like' => ['42.0', 42.0],
            ]);

            it('throws exception on invalid string', function (string $input, string $exception, string $message) {
                expect(fn() => FloatNonNegative::fromString($input))->toThrow($exception, $message);
            })->with([
                'empty' => ['', StringTypeException::class, 'String "" has no valid float value'],
                'non-numeric' => ['abc', StringTypeException::class, 'String "abc" has no valid float value'],
                'comma separator' => ['5,5', StringTypeException::class, 'String "5,5" has no valid float value'],
                'negative' => ['-1.0', FloatTypeException::class, 'Expected non-negative float, got "-1"'],
                'negative small' => ['-0.10000000000000001', FloatTypeException::class, 'Expected non-negative float, got "-0.1"'],
            ]);
        });

        describe('tryFromFloat', function () {
            it('returns instance or default value', function (float $input, mixed $expected) {
                $result = FloatNonNegative::tryFromFloat($input);
                if ($expected instanceof FloatNonNegative) {
                    expect($result)->toBeInstanceOf(FloatNonNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'positive' => [2.0, FloatNonNegative::fromFloat(2.0)],
                'zero' => [0.0, FloatNonNegative::fromFloat(0.0)],
                'negative' => [-1.0, Undefined::create()],
            ]);
        });

        describe('fromFloat', function () {
            it('creates instance from valid non-negative float', function (float $input) {
                expect(FloatNonNegative::fromFloat($input)->value())->toBe($input);
            })->with([
                'standard positive' => [1.5],
                'zero' => [0.0],
                'negative zero' => [-0.0],
            ]);

            it('throws exception on invalid float', function (float $input, string $message) {
                expect(fn() => FloatNonNegative::fromFloat($input))->toThrow(FloatTypeException::class, $message);
            })->with([
                'negative' => [-1.0, 'Expected non-negative float, got "-1"'],
                'negative small' => [-0.001, 'Expected non-negative float, got "-0.001"'],
            ]);
        });

        describe('tryFromInt', function () {
            it('returns instance or default value', function (int $input, mixed $expected) {
                $result = FloatNonNegative::tryFromInt($input);
                if ($expected instanceof FloatNonNegative) {
                    expect($result)->toBeInstanceOf(FloatNonNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'positive int' => [5, FloatNonNegative::fromFloat(5.0)],
                'zero int' => [0, FloatNonNegative::fromFloat(0.0)],
                'negative int' => [-5, Undefined::create()],
            ]);
        });

        describe('tryFromBool', function () {
            it('returns instance or default value', function (bool $input, mixed $expected) {
                $result = FloatNonNegative::tryFromBool($input);
                if ($expected instanceof FloatNonNegative) {
                    expect($result)->toBeInstanceOf(FloatNonNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'true' => [true, FloatNonNegative::fromFloat(1.0)],
                'false' => [false, FloatNonNegative::fromFloat(0.0)],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, float $expected) {
                $result = FloatNonNegative::tryFromMixed($input);
                expect($result)->toBeInstanceOf(FloatNonNegative::class)
                    ->and($result->value())->toBe($expected);
            })->with([
                'float 1.5' => [1.5, 1.5],
                'float 0.0' => [0.0, 0.0],
                'PHP_FLOAT_MAX' => [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                'long float' => [1.234567890123456789, 1.234567890123456789],
                '2/3' => [2 / 3, 2 / 3],
                'FloatNonNegative instance' => [FloatNonNegative::fromFloat(1.234567890123456789), 1.234567890123456789],
                'another FloatNonNegative instance' => [FloatNonNegative::fromFloat(4.5), 4.5],
                'int 1' => [1, 1.0],
                'int 0' => [0, 0.0],
                'int 111' => [111, 111.0],
                'bool true' => [true, 1.0],
                'bool false' => [false, 0.0],
                'string 1.5' => ['1.5', 1.5],
                'string 0.0' => ['0.0', 0.0],
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

            it('works with closure', function () {
                expect(FloatNonNegative::tryFromMixed(fn() => 1.5))
                    ->toBeInstanceOf(Undefined::class);
            });

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = FloatNonNegative::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [[]],
                'object' => [new stdClass()],
                'non-numeric string' => ['not-a-float'],
                'invalid format string' => ['1.2.3'],
                'octal-like string' => ['007'],
                'Callable array' => [['FloatNonNegative', 'fromInt']],
                'Resource' => [fopen('php://memory', 'r')],
                'Array of objects' => [[new stdClass()]],
                'INF' => [\INF],
                'NAN' => [\NAN],
                'Null byte string' => ["\0"],
                'negative float' => [-3.14],
                'negative integer' => [-42],
                'negative string' => ['-10.5'],
            ]);
        });

        describe('Constructor', function () {
            it('constructs non-negative float via constructor', function () {
                $v = new FloatNonNegative(0.0);
                expect($v->value())->toBe(0.0)
                    ->and($v->toString())->toBe('0.0');
            });

            it('throws on negative values', function (float $input, string $message) {
                expect(fn() => new FloatNonNegative($input))
                    ->toThrow(FloatTypeException::class, $message);
            })->with([
                'negative' => [-0.1, 'Expected non-negative float, got "-0.1"'],
                'negative small' => [-0.001, 'Expected non-negative float, got "-0.001"'],
                'very small negative' => [-1e-308, 'Expected non-negative float, got "-1.0E-308"'],
            ]);
        });
    });

    describe('Instance Methods', function () {
        it('value() returns the internal float value', function () {
            expect(FloatNonNegative::fromFloat(1.5)->value())->toBe(1.5);
        });

        describe('toString and __toString', function () {
            it('returns string representation', function (float $value, string $expected) {
                $f = FloatNonNegative::fromFloat($value);
                expect($f->toString())->toBe($expected)
                    ->and((string) $f)->toBe($expected);
            })->with([
                '0.0' => [0.0, '0.0'],
                '1.5' => [1.5, '1.5'],
                '2.5' => [2.5, '2.5'],
                '42.0' => [42.0, '42.0'],
                'negative zero normalized' => [-0.0, '0.0'],
            ]);
        });

        it('jsonSerialize() returns float', function () {
            $v = FloatNonNegative::fromString('10.5');
            expect($v->jsonSerialize())->toBeFloat()
                ->and($v->jsonSerialize())->toBe($v->value());

            expect(FloatNonNegative::tryFromString('1.10000000000000009')->jsonSerialize())->toBeFloat();
        });

        it('isEmpty() returns false', function () {
            expect((new FloatNonNegative(0.0))->isEmpty())->toBeFalse()
                ->and(FloatNonNegative::fromFloat(3.14)->isEmpty())->toBeFalse();
        });

        it('isUndefined() returns false for instances and true for Undefined results', function () {
            $v1 = new FloatNonNegative(0.0);
            $v2 = FloatNonNegative::fromFloat(1.0);
            $u1 = FloatNonNegative::tryFromString('-0.10000000000000001');
            $u2 = FloatNonNegative::tryFromMixed('abc');
            $u3 = FloatNonNegative::tryFromFloat(-1.0);

            expect($v1->isUndefined())->toBeFalse()
                ->and($v2->isUndefined())->toBeFalse()
                ->and($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue()
                ->and($u3->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $v = FloatNonNegative::fromFloat(1.5);
                expect($v->isTypeOf(FloatNonNegative::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $v = FloatNonNegative::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $v = FloatNonNegative::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass', FloatNonNegative::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });

    describe('toDecimal', function () {
        it('converts to decimal string', function (float $value, string $expected) {
            expect(FloatNonNegative::fromFloat($value)->toDecimal())->toBe($expected);
        })->with([
            'standard float' => [1.5, '1.5'],
            'zero' => [0.0, '0.0'],
        ]);

        it('throws on precision loss when converting to decimal', function () {
            expect(fn() => FloatNonNegative::fromFloat(1e-308)->toDecimal())->toThrow(FloatTypeException::class);
        });
    });

    describe('Conversions', function () {
        it('converts to bool', function (float $value, bool $expected) {
            expect(FloatNonNegative::fromFloat($value)->toBool())->toBe($expected);
        })->with([
            '1.0 to true' => [1.0, true],
            '0.0 to false' => [0.0, false],
        ]);

        it('throws when converting non-integer-like float to bool', function () {
            expect(fn() => FloatNonNegative::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class);
        });

        it('converts to int', function (float $value, int $expected) {
            expect(FloatNonNegative::fromFloat($value)->toInt())->toBe($expected);
        })->with([
            '1.0 to 1' => [1.0, 1],
            '0.0 to 0' => [0.0, 0],
        ]);

        it('throws when converting non-integer-like float to int', function () {
            expect(fn() => FloatNonNegative::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);
        });

        it('converts to float', function () {
            expect(FloatNonNegative::fromFloat(1.0)->toFloat())->toBe(1.0);
        });
    });
});
