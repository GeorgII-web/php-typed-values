<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerStandard', function () {
    describe('Creation', function () {
        describe('fromInt', function () {
            it('creates instance from integer', function (int $input) {
                $v = IntegerStandard::fromInt($input);
                expect($v->value())->toBe($input)
                    ->and($v->toString())->toBe((string) $input);
            })->with([
                'positive' => [123],
                'negative' => [-42],
                'zero' => [0],
                'PHP_INT_MIN' => [\PHP_INT_MIN],
                'PHP_INT_MAX' => [\PHP_INT_MAX],
            ]);
        });

        describe('fromString', function () {
            it('creates instance from valid string', function (string $input, int $expected) {
                expect(IntegerStandard::fromString($input)->value())->toBe($expected);
            })->with([
                'positive' => ['123', 123],
                'negative' => ['-42', -42],
                'zero' => ['0', 0],
            ]);

            it('throws exception on invalid or non-canonical strings', function (string $input) {
                expect(fn() => IntegerStandard::fromString($input))->toThrow(StringTypeException::class);
            })->with([
                'non-numeric' => ['5a'],
                'leading alpha' => ['a5'],
                'empty' => [''],
                'alpha' => ['abc'],
                'leading space' => [' 5'],
                'trailing space' => ['5 '],
                'explicit positive' => ['+5'],
                'leading zero' => ['05'],
                'double negative' => ['--5'],
                'float string' => ['3.14'],
            ]);
        });

        describe('fromFloat', function () {
            it('creates instance from integer-equivalent float', function (float $input, int $expected) {
                expect(IntegerStandard::fromFloat($input)->value())->toBe($expected);
            })->with([
                'zero' => [0.0, 0],
                'one' => [1.0, 1],
                'negative' => [-1.0, -1],
                'PHP_INT_MIN as float' => [(float) \PHP_INT_MIN, \PHP_INT_MIN],
            ]);

            it('throws exception on non-integer float or out of range', function (float $input) {
                expect(fn() => IntegerStandard::fromFloat($input))->toThrow(FloatTypeException::class);
            })->with([
                'with decimal' => [1.1],
                'negative decimal' => [-0.1],
                'too big' => [1e20],
                'PHP_INT_MAX as float' => [(float) \PHP_INT_MAX],
                'below MIN' => [(float) \PHP_INT_MIN - 4096.0],
                'above MAX' => [(float) \PHP_INT_MAX + 2048.0],
            ]);
        });

        describe('fromBool', function () {
            it('creates instance from boolean', function (bool $input, int $expected) {
                expect(IntegerStandard::fromBool($input)->value())->toBe($expected);
            })->with([
                'true' => [true, 1],
                'false' => [false, 0],
            ]);
        });

        describe('tryFrom* methods', function () {
            it('tryFromInt returns instance', function () {
                expect(IntegerStandard::tryFromInt(10)->value())->toBe(10);
            });

            it('tryFromString returns instance or default', function (string $input, mixed $expected) {
                $result = IntegerStandard::tryFromString($input);
                if ($expected instanceof IntegerStandard) {
                    expect($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['123', IntegerStandard::fromInt(123)],
                'invalid' => ['abc', Undefined::create()],
            ]);

            it('tryFromFloat returns instance or default', function (float $input, mixed $expected) {
                $result = IntegerStandard::tryFromFloat($input);
                if ($expected instanceof IntegerStandard) {
                    expect($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => [1.0, IntegerStandard::fromInt(1)],
                'invalid' => [1.1, Undefined::create()],
            ]);

            it('tryFromBool returns instance', function () {
                expect(IntegerStandard::tryFromBool(true)->value())->toBe(1);
            });

            it('tryFromMixed returns instance or default', function (mixed $input, mixed $expected) {
                $result = IntegerStandard::tryFromMixed($input);
                if ($expected instanceof IntegerStandard) {
                    expect($result->value())->toBe($expected->value());
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'int' => [123, IntegerStandard::fromInt(123)],
                'float' => [1.0, IntegerStandard::fromInt(1)],
                'bool' => [true, IntegerStandard::fromInt(1)],
                'string' => ['42', IntegerStandard::fromInt(42)],
                'stringable' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return '100';
                        }
                    },
                    IntegerStandard::fromInt(100),
                ],
                'null' => [null, Undefined::create()],
                'array' => [[], Undefined::create()],
                'object' => [new stdClass(), Undefined::create()],
            ]);
        });
    });

    describe('Conversions', function () {
        it('toInt returns internal value', function () {
            expect(IntegerStandard::fromInt(123)->toInt())->toBe(123);
        });

        it('toFloat returns float representation', function () {
            expect(IntegerStandard::fromInt(123)->toFloat())->toBe(123.0)
                ->and(IntegerStandard::fromInt(123)->toFloat())->toBeFloat();
        });

        it('throws exception when toFloat loses precision', function () {
            // PHP_INT_MAX is usually not representable exactly as float
            $v = IntegerStandard::fromInt(\PHP_INT_MAX);
            expect(fn() => $v->toFloat())->toThrow(IntegerTypeException::class);
        });

        it('toBool returns boolean representation', function (int $input, bool $expected) {
            expect(IntegerStandard::fromInt($input)->toBool())->toBe($expected);
        })->with([
            'zero is false' => [0, false],
            'non-zero is true' => [1, true],
            'negative is true' => [-1, true],
        ]);

        it('toString and __toString return string representation', function () {
            $v = IntegerStandard::fromInt(123);
            expect($v->toString())->toBe('123')
                ->and((string) $v)->toBe('123');
        });

        it('jsonSerialize returns integer', function () {
            expect(IntegerStandard::fromInt(123)->jsonSerialize())->toBe(123);
        });
    });

    describe('Information', function () {
        it('value() returns internal integer', function () {
            expect(IntegerStandard::fromInt(123)->value())->toBe(123);
        });

        it('isEmpty() always returns false', function () {
            expect(IntegerStandard::fromInt(0)->isEmpty())->toBeFalse();
        });

        it('isUndefined() always returns false', function () {
            expect(IntegerStandard::fromInt(0)->isUndefined())->toBeFalse();
        });

        it('isTypeOf identifies class correctly', function () {
            $v = IntegerStandard::fromInt(123);
            expect($v->isTypeOf(IntegerStandard::class))->toBeTrue()
                ->and($v->isTypeOf('NonExistent'))->toBeFalse()
                ->and($v->isTypeOf('NonExistent', IntegerStandard::class))->toBeTrue();
        });
    });
});
