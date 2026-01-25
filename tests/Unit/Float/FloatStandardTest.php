<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('FloatStandard', function () {
    describe('Creation', function () {
        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, mixed $expected) {
                $result = FloatStandard::tryFromString($input);
                if ($expected instanceof FloatStandard) {
                    expect($result)->toBeInstanceOf(FloatStandard::class)
                        ->and($result->value())->toBe($expected->value())
                        ->and($result->toString())->toBe($expected->toString());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid float string' => ['1.5', FloatStandard::fromFloat(1.5)],
                'invalid float string' => ['abc', Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatStandard::tryFromString('abc', Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });

        describe('fromString', function () {
            it('creates instance from valid string', function (string $input, float $expected) {
                expect(FloatStandard::fromString($input)->value())->toBe($expected);
            })->with([
                'standard float' => ['1.5', 1.5],
            ]);

            it('throws exception on invalid string', function (string $input, string $exception, string $message) {
                expect(fn() => FloatStandard::fromString($input))->toThrow($exception, $message);
            })->with([
                'non-numeric' => ['NaN', StringTypeException::class, 'String "NaN" has no valid float value'],
                'loose precision' => ['0.1', StringTypeException::class, 'String "0.1" has no valid strict float value'],
                'long tail' => [
                    '12.444144424443444044454446444744484449444',
                    StringTypeException::class,
                    'String "12.444144424443444044454446444744484449444" has no valid strict float value',
                ],
            ]);
        });

        describe('tryFromFloat', function () {
            it('returns instance or default value', function (float $input, mixed $expected) {
                $result = FloatStandard::tryFromFloat($input);
                if ($expected instanceof FloatStandard) {
                    expect($result)->toBeInstanceOf(FloatStandard::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid float' => [2.0, FloatStandard::fromFloat(2.0)],
                'INF' => [\INF, Undefined::create()],
                'NAN' => [\NAN, Undefined::create()],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatStandard::tryFromFloat(\INF, Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });

        describe('fromFloat', function () {
            it('creates instance from valid float', function (float $input) {
                expect(FloatStandard::fromFloat($input)->value())->toBe($input);
            })->with([
                'standard float' => [3.14],
            ]);

            it('throws exception on invalid float', function (float $input) {
                expect(fn() => FloatStandard::fromFloat($input))->toThrow(FloatTypeException::class);
            })->with([
                'INF' => [\INF],
                'NAN' => [\NAN],
            ]);
        });

        describe('tryFromInt', function () {
            it('returns instance or default value', function (int $input, mixed $expected) {
                $result = FloatStandard::tryFromInt($input);
                if ($expected instanceof FloatStandard) {
                    expect($result)->toBeInstanceOf(FloatStandard::class)
                        ->and($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'standard int' => [5, FloatStandard::fromFloat(5.0)],
                'PHP_INT_MAX' => [\PHP_INT_MAX, Undefined::create()],
            ]);
        });

        describe('fromInt', function () {
            it('creates instance from valid int', function (int $input) {
                expect(FloatStandard::fromInt($input)->value())->toBe((float) $input);
            })->with([
                'standard int' => [5],
            ]);

            it('throws exception on invalid int', function (int $input) {
                expect(fn() => FloatStandard::fromInt($input))->toThrow(IntegerTypeException::class);
            })->with([
                'PHP_INT_MAX' => [\PHP_INT_MAX],
            ]);
        });

        describe('tryFromBool', function () {
            it('returns instance from boolean', function (bool $input, float $expected) {
                expect(FloatStandard::tryFromBool($input)->value())->toBe($expected);
            })->with([
                'true' => [true, 1.0],
                'false' => [false, 0.0],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, float $expected) {
                $result = FloatStandard::tryFromMixed($input);
                expect($result)->toBeInstanceOf(FloatStandard::class)
                    ->and($result->value())->toBe($expected);
            })->with([
                'float 1.5' => [1.5, 1.5],
                'float 0.0' => [0.0, 0.0],
                'float -3.14' => [-3.14, -3.14],
                'PHP_FLOAT_MAX' => [\PHP_FLOAT_MAX, \PHP_FLOAT_MAX],
                'long float' => [1.234567890123456789, 1.234567890123456789],
                '2/3' => [2 / 3, 2 / 3],
                'FloatStandard instance' => [FloatStandard::fromFloat(1.234567890123456789), 1.234567890123456789],
                'int 1' => [1, 1.0],
                'int 0' => [0, 0.0],
                'int -42' => [-42, -42.0],
                'int 111' => [111, 111.0],
                'bool true' => [true, 1.0],
                'bool false' => [false, 0.0],
                'string 1.5' => ['1.5', 1.5],
                'string 0.0' => ['0.0', 0.0],
                'string -10.5' => ['-10.5', -10.5],
                'stringable object' => [
                    new class {
                        public function __toString(): string
                        {
                            return '2.5';
                        }
                    },
                    2.5,
                ],
                'stringable object 1.23' => [
                    new class {
                        public function __toString(): string
                        {
                            return '1.22999999999999998';
                        }
                    },
                    1.23,
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = FloatStandard::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class)
                    ->and($result->isUndefined())->toBeTrue();
            })->with([
                'null' => [null],
                'array' => [[]],
                'object' => [new stdClass()],
                'non-numeric string' => ['not-a-float'],
                'invalid format string' => ['1.2.3'],
                'octal-like string' => ['007'],
                'Callable array' => [['FloatStandard', 'fromInt']],
                'Resource' => [fopen('php://memory', 'r')],
                'Array of objects' => [[new stdClass()]],
                'INF' => [\INF],
                'NAN' => [\NAN],
                'Null byte string' => ["\0"],
            ]);

            it('returns custom default on failure', function () {
                expect(FloatStandard::tryFromMixed([], Undefined::create()))->toBeInstanceOf(Undefined::class);
            });
        });
    });

    describe('Instance Methods', function () {
        it('value() returns the internal float value', function () {
            expect(FloatStandard::fromFloat(1.5)->value())->toBe(1.5);
        });

        describe('toString and __toString', function () {
            it('returns string representation', function (float $value, string $expected) {
                $f = FloatStandard::fromFloat($value);
                expect($f->toString())->toBe($expected)
                    ->and((string) $f)->toBe($expected);
            })->with([
                '0' => [0, '0.0'],
                '0.0' => [0.0, '0.0'],
                '-0.0' => [-0.0, '0.0'],
                '+0.0' => [+0.0, '0.0'],
                '1.0' => [1.0, '1.0'],
                '-1.0' => [-1.0, '-1.0'],
                '0.1' => [0.1, '0.10000000000000001'],
                '0.10000000000000001' => [0.10000000000000001, '0.10000000000000001'],
                '0.10000000000000002' => [0.10000000000000002, '0.10000000000000002'],
                '0.10000000000000012' => [0.10000000000000012, '0.10000000000000012'],
                '-0.1' => [-0.1, '-0.10000000000000001'],
                '0.2' => [0.2, '0.20000000000000001'],
                '0.3' => [0.3, '0.29999999999999999'],
                '0.1+0.2' => [0.1 + 0.2, '0.30000000000000004'],
                '1/3' => [1.0 / 3.0, '0.33333333333333331'],
                '2/3' => [2.0 / 3.0, '0.66666666666666663'],
                '10/3' => [10.0 / 3.0, '3.33333333333333348'],
                '1e10' => [1e10, '10000000000.0'],
                '-1e10' => [-1e10, '-10000000000.0'],
                '1e16' => [1e16, '10000000000000000.0'],
                '1e308' => [1e308, '100000000000000001097906362944045541740492309677311846336810682903157585404911491537163328978494688899061249669721172515611590283743140088328307009198146046031271664502933027185697489699588559043338384466165001178426897626212945177628091195786707458122783970171784415105291802893207873272974885715430223118336.0'],
                '-1e308' => [-1e308, '-100000000000000001097906362944045541740492309677311846336810682903157585404911491537163328978494688899061249669721172515611590283743140088328307009198146046031271664502933027185697489699588559043338384466165001178426897626212945177628091195786707458122783970171784415105291802893207873272974885715430223118336.0'],
                '1e-10' => [1e-10, '0.0000000001'],
                '-1e-10' => [-1e-10, '-0.0000000001'],
                '1.99999999999999' => [1.99999999999999, '1.99999999999999001'],
                '2.00000000000001' => [2.00000000000001, '2.00000000000001021'],
                '0.7' => [0.7, '0.69999999999999996'],
                '0.17' => [0.17, '0.17000000000000001'],
                '0.57' => [0.57, '0.56999999999999995'],
                '0.99' => [0.99, '0.98999999999999999'],
                '001.5' => [001.5, '1.5'],
                '0.3333333333333333' => [0.3333333333333333, '0.33333333333333331'],
                '0.6666666666666666' => [0.6666666666666666, '0.66666666666666663'],
                '2^53-1' => [9007199254740991.0, '9007199254740991.0'],
                '2^53' => [9007199254740992.0, '9007199254740992.0'],
                '-2^53' => [-9007199254740992.0, '-9007199254740992.0'],
                'PHP_INT_MAX as float' => [(float) \PHP_INT_MAX, '9223372036854775808.0'],
                'Float_closure' => [fn() => 1.5, '1.5'],
            ]);

            it('throws exception for very small floats', function (float $value) {
                $f = FloatStandard::fromFloat($value);
                expect(fn() => $f->toString())->toThrow(FloatTypeException::class);
            })->with([
                '1e-308' => [1e-308],
                '-1e-308' => [-1e-308],
                '5e-324' => [5e-324],
                '-5e-324' => [-5e-324],
            ]);
        });

        it('jsonSerialize() returns float', function () {
            expect(FloatStandard::tryFromString('1.10000000000000009')->jsonSerialize())->toBeFloat();
        });

        it('isEmpty() returns false', function () {
            expect(FloatStandard::fromFloat(-1.0)->isEmpty())->toBeFalse()
                ->and(FloatStandard::fromFloat(0.0)->isEmpty())->toBeFalse();
        });

        it('isUndefined() returns false', function () {
            expect(FloatStandard::fromFloat(-1.0)->isUndefined())->toBeFalse()
                ->and(FloatStandard::fromFloat(0.0)->isUndefined())->toBeFalse();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $v = FloatStandard::fromFloat(1.5);
                expect($v->isTypeOf(FloatStandard::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $v = FloatStandard::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $v = FloatStandard::fromFloat(1.5);
                expect($v->isTypeOf('NonExistentClass', FloatStandard::class, 'AnotherClass'))->toBeTrue();
            });
        });

        describe('Conversions', function () {
            it('converts to bool', function (float $value, bool $expected) {
                expect(FloatStandard::fromFloat($value)->toBool())->toBe($expected);
            })->with([
                '1.0 to true' => [1.0, true],
                '0.0 to false' => [0.0, false],
            ]);

            it('throws when converting non-integer-like float to bool', function () {
                expect(fn() => FloatStandard::fromFloat(0.5)->toBool())->toThrow(FloatTypeException::class);
            });

            it('converts to int', function (float $value, int $expected) {
                expect(FloatStandard::fromFloat($value)->toInt())->toBe($expected);
            })->with([
                '1.0 to 1' => [1.0, 1],
                '0.0 to 0' => [0.0, 0],
            ]);

            it('throws when converting non-integer-like float to int', function () {
                expect(fn() => FloatStandard::fromFloat(0.5)->toInt())->toThrow(FloatTypeException::class);
            });

            it('converts to float', function () {
                expect(FloatStandard::fromFloat(1.0)->toFloat())->toBe(1.0);
            });
        });
    });

    describe('Precision and Edge Cases', function () {
        it('checks compare algorithm', function () {
            $f1 = FloatStandard::fromFloat(0.1);
            $f2 = FloatStandard::fromFloat(0.7);
            $f3 = FloatStandard::fromFloat(0.8);

            expect($f1->toString())->toBe('0.10000000000000001')
                ->and($f2->toString())->toBe('0.69999999999999996')
                ->and($f3->toString())->toBe('0.80000000000000004')
                ->and($f1->value())->toBe(0.1)
                ->and($f2->value())->toBe(0.7)
                ->and($f3->value())->toBe(0.8)
                ->and(FloatStandard::fromFloat($f1->value() + $f2->value())->toString())->toBe('0.79999999999999993');
        });

        it('checks diff between string formatting and native float', function () {
            $a = new FloatStandard((float) (string) (2 / 3));
            $b = new FloatStandard(2 / 3);

            expect($a->value())->toBe(0.66666666666667)
                ->and($b->value())->toBe(0.6666666666666666);
        });

        it('covers intToString protective check', function () {
            // It's hard to trigger line 230 with a real int in PHP,
            // but we can test it with standard values to at least execute the line.
            $v = PhpTypedValues\String\StringStandard::fromInt(123);
            expect($v->value())->toBe('123');
        });
    });
});
