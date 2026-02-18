<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Float\FloatNegative;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('FloatNegative', function () {
    describe('Creation', function () {
        describe('fromDecimal', function () {
            it('creates instance from valid negative decimal string', function (string $input, float $expected) {
                expect(FloatNegative::fromDecimal($input)->value())->toBe($expected);
            })->with([
                'negative decimal' => ['-1.5', -1.5],
            ]);

            it('throws exception on invalid decimal string', function (string $input, string $exception) {
                expect(fn() => FloatNegative::fromDecimal($input))->toThrow($exception);
            })->with([
                'non-numeric' => ['abc', DecimalTypeException::class],
                'integer format' => ['-123', StringTypeException::class],
                'zero decimal' => ['0.0', TypeException::class],
                'positive decimal' => ['1.5', TypeException::class],
            ]);
        });

        describe('tryFromDecimal', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatNegative::tryFromDecimal($input);
                if ($expected instanceof FloatNegative) {
                    expect($result)->toBeInstanceOf(FloatNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid decimal' => ['-1.5', FloatNegative::fromFloat(-1.5)],
                'zero decimal' => ['0.0', Undefined::create()],
                'positive decimal' => ['1.5', Undefined::create()],
                'invalid decimal' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromDecimal('0.0', $customDefault))->toBe($customDefault);
            });
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatNegative::tryFromString($input);
                if ($expected instanceof FloatNegative) {
                    expect($result)->toBeInstanceOf(FloatNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid negative' => ['-0.10000000000000001', FloatNegative::fromFloat(-0.1)],
                'zero' => ['0.0', Undefined::create()],
                'positive' => ['0.10000000000000001', Undefined::create()],
                'non-numeric' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromString('0.0', $customDefault))->toBe($customDefault);
            });
        });

        describe('fromString', function () {
            it('creates instance from valid negative string', function (string $input, float $expected) {
                expect(FloatNegative::fromString($input)->value())->toBe($expected);
            })->with([
                'standard negative' => ['-2.5', -2.5],
                'another negative' => ['-1.25', -1.25],
            ]);

            it('throws exception on invalid string', function (string $input, string $exception, string $message) {
                expect(fn() => FloatNegative::fromString($input))->toThrow($exception, $message);
            })->with([
                'zero' => ['0.0', FloatTypeException::class, 'Expected negative float, got "0"'],
                'positive' => ['1.23', StringTypeException::class, 'String "1.23" has no valid strict float value'],
                'non-numeric' => ['unknown', StringTypeException::class, 'String "unknown" has no valid float value'],
            ]);
        });

        describe('tryFromFloat', function () {
            it('returns instance or default value', function (float $input, mixed $expected) {
                $result = FloatNegative::tryFromFloat($input);
                if ($expected instanceof FloatNegative) {
                    expect($result)->toBeInstanceOf(FloatNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'negative' => [-2.0, FloatNegative::fromFloat(-2.0)],
                'zero' => [0.0, Undefined::create()],
                'positive' => [2.0, Undefined::create()],
                'INF' => [\INF, Undefined::create()],
                'NAN' => [\NAN, Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromFloat(0.0, $customDefault))->toBe($customDefault);
            });
        });

        describe('fromFloat', function () {
            it('creates instance from valid negative float', function (float $input) {
                expect(FloatNegative::fromFloat($input)->value())->toBe($input);
            })->with([
                'standard negative' => [-1.5],
            ]);

            it('throws exception on invalid float', function (float $input, string $message) {
                expect(fn() => FloatNegative::fromFloat($input))->toThrow(FloatTypeException::class, $message);
            })->with([
                'zero' => [0.0, 'Expected negative float, got "0"'],
                'positive' => [1.0, 'Expected negative float, got "1"'],
            ]);
        });

        describe('tryFromInt', function () {
            it('returns instance or default value', function (int $input, mixed $expected) {
                $result = FloatNegative::tryFromInt($input);
                if ($expected instanceof FloatNegative) {
                    expect($result)->toBeInstanceOf(FloatNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'negative int' => [-5, FloatNegative::fromFloat(-5.0)],
                'zero int' => [0, Undefined::create()],
                'positive int' => [5, Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromInt(0, $customDefault))->toBe($customDefault);
            });
        });

        describe('tryFromBool', function () {
            it('returns instance or default value', function (bool $input, mixed $expected) {
                $result = FloatNegative::tryFromBool($input);
                if ($expected instanceof FloatNegative) {
                    expect($result)->toBeInstanceOf(FloatNegative::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'true' => [true, Undefined::create()],
                'false' => [false, Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromBool(true, $customDefault))->toBe($customDefault);
            });
        });

        describe('fromBool', function () {
            it('throws on fromBool', function (bool $input) {
                expect(fn() => FloatNegative::fromBool($input))->toThrow(FloatTypeException::class);
            })->with([
                'true' => [true],
                'false' => [false],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns Undefined for invalid mixed input values', function (mixed $input) {
                $result = FloatNegative::tryFromMixed($input);
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
                'positive float' => [3.14],
                'positive integer' => [42],
                'positive string' => ['10.5'],
            ]);

            it('returns instance for valid mixed inputs', function (mixed $input, float $expected) {
                $result = FloatNegative::tryFromMixed($input);
                expect($result)->toBeInstanceOf(FloatNegative::class)
                    ->and($result->value())->toBe($expected);
            })->with([
                'float -1.5' => [-1.5, -1.5],
                'PHP_FLOAT_MIN' => [-\PHP_FLOAT_MAX, -\PHP_FLOAT_MAX],
                'long float' => [-1.234567890123456789, -1.234567890123456789],
                'FloatNegative instance' => [FloatNegative::fromFloat(-1.234567890123456789), -1.234567890123456789],
                'int -1' => [-1, -1.0],
                'int -111' => [-111, -111.0],
                'string -1.5' => ['-1.5', -1.5],
                'string -0.1' => ['-0.10000000000000001', -0.1],
                'stringable object' => [
                    new class {
                        public function __toString(): string
                        {
                            return '-2.5';
                        }
                    },
                    -2.5,
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = FloatNegative::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class);
            })->with([
                'null' => [null],
                'array' => [[]],
                'object' => [new stdClass()],
                'non-numeric string' => ['not-a-float'],
                'zero float' => [0.0],
                'zero integer' => [0],
                'false boolean' => [false],
                'true boolean' => [true],
                'positive float' => [3.14],
                'positive integer' => [42],
                'positive string' => ['10.5'],
                'INF' => [\INF],
                'NAN' => [\NAN],
                'resource' => [fn() => fopen('php://memory', 'r')],
                'closure' => [fn() => fn() => -1.0],
            ]);

            it('returns custom default on failure', function () {
                $customDefault = Undefined::create();
                expect(FloatNegative::tryFromMixed(0.0, $customDefault))->toBe($customDefault);
            });
        });

        describe('Constructor', function () {
            it('constructs negative float via constructor', function () {
                $v = new FloatNegative(-0.1);
                expect($v->value())->toBe(-0.1)
                    ->and($v->toString())->toBe('-0.10000000000000001');
            });

            it('throws on non-negative values', function (float $input, string $message) {
                expect(fn() => new FloatNegative($input))
                    ->toThrow(FloatTypeException::class, $message);
            })->with([
                'zero' => [0.0, 'Expected negative float, got "0"'],
                'positive' => [0.1, 'Expected negative float, got "0.1"'],
                'INF' => [\INF, 'Expected negative float, got "INF"'],
                '-INF' => [-\INF, 'Infinite float value'],
                'NAN' => [\NAN, 'Not a number float value'],
            ]);
        });
    });

    describe('Instance Methods', function () {
        it('value() returns the internal float value', function () {
            expect(FloatNegative::fromFloat(-1.5)->value())->toBe(-1.5);
        });

        describe('toString and __toString', function () {
            it('returns string representation', function (float $value, string $expected) {
                $f = FloatNegative::fromFloat($value);
                expect($f->toString())->toBe($expected)
                    ->and((string) $f)->toBe($expected);
            })->with([
                '-3.14' => [-3.14, '-3.14000000000000012'],
                '-1.0' => [-1.0, '-1.0'],
            ]);
        });

        it('jsonSerialize() returns float', function () {
            expect(FloatNegative::tryFromString('-1.10000000000000009')->jsonSerialize())->toBeFloat();
        });

        it('isEmpty() returns false', function () {
            expect(FloatNegative::fromFloat(-0.1)->isEmpty())->toBeFalse();
        });

        it('isUndefined() returns false for instances and true for Undefined results', function () {
            $v1 = new FloatNegative(-0.1);
            $u1 = FloatNegative::tryFromString('0.0');
            $u2 = FloatNegative::tryFromMixed('abc');
            $u3 = FloatNegative::tryFromFloat(0.0);

            expect($v1->isUndefined())->toBeFalse()
                ->and($u1->isUndefined())->toBeTrue()
                ->and($u2->isUndefined())->toBeTrue()
                ->and($u3->isUndefined())->toBeTrue();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $v = FloatNegative::fromFloat(-1.5);
                expect($v->isTypeOf(FloatNegative::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $v = FloatNegative::fromFloat(-1.5);
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $v = FloatNegative::fromFloat(-1.5);
                expect($v->isTypeOf('NonExistentClass', FloatNegative::class, 'AnotherClass'))->toBeTrue();
            });
        });
    });

    describe('toDecimal', function () {
        it('converts to decimal string', function (float $value, string $expected) {
            expect(FloatNegative::fromFloat($value)->toDecimal())->toBe($expected);
        })->with([
            'standard float' => [-1.5, '-1.5'],
        ]);

        it('throws on precision loss when converting to decimal', function () {
            expect(fn() => FloatNegative::fromFloat(-1e-308)->toDecimal())->toThrow(FloatTypeException::class);
        });
    });

    describe('Conversions', function () {
        it('throws when converting to bool', function (float $value) {
            expect(fn() => FloatNegative::fromFloat($value)->toBool())->toThrow(FloatTypeException::class);
        })->with([
            '-1.0' => [-1.0],
            '-0.5' => [-0.5],
        ]);

        it('converts to int', function (float $value, int $expected) {
            expect(FloatNegative::fromFloat($value)->toInt())->toBe($expected);
        })->with([
            '-1.0 to -1' => [-1.0, -1],
        ]);

        it('throws when converting non-integer-like float to int', function () {
            expect(fn() => FloatNegative::fromFloat(-0.5)->toInt())->toThrow(FloatTypeException::class);
        });

        it('converts to float', function () {
            expect(FloatNegative::fromFloat(-1.0)->toFloat())->toBe(-1.0);
        });
    });
});
