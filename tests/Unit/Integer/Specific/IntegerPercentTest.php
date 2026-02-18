<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Integer\Specific\IntegerPercent;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('IntegerPercent', function () {
    describe('Factories', function () {
        it('creates from bool', function () {
            expect(IntegerPercent::fromBool(true)->value())->toBe(1)
                ->and(IntegerPercent::fromBool(false)->value())->toBe(0);
        });

        it('creates from float', function (float $input, int $expected) {
            expect(IntegerPercent::fromFloat($input)->value())->toBe($expected);
        })->with([
            'zero' => [0.0, 0],
            'one' => [1.0, 1],
            'fifty' => [50.0, 50],
            'hundred' => [100.0, 100],
        ]);

        it('throws when creating from invalid float', function (float $input, string $exception, string $message) {
            expect(fn() => IntegerPercent::fromFloat($input))->toThrow($exception, $message);
        })->with([
            'negative' => [-1.0, IntegerTypeException::class, 'Expected percent integer, got "-1"'],
            'too large' => [101.0, IntegerTypeException::class, 'Expected percent integer, got "101"'],
            'with precision' => [1.5, FloatTypeException::class, 'Float "1.5" has no valid strict int value'],
            'INF' => [\INF, FloatTypeException::class, 'Float "INF" has no valid strict int value'],
            'NAN' => [\NAN, FloatTypeException::class, 'Float "NAN" has no valid strict int value'],
        ]);

        it('creates from int', function (int $input) {
            expect(IntegerPercent::fromInt($input)->value())->toBe($input);
        })->with([
            'zero' => [0],
            'one' => [1],
            'fifty' => [50],
            'hundred' => [100],
        ]);

        it('throws when creating from invalid int', function (int $input) {
            expect(fn() => IntegerPercent::fromInt($input))->toThrow(IntegerTypeException::class, "Expected percent integer, got \"{$input}\"");
        })->with([
            'negative' => [-1],
            'too large' => [101],
            'min' => [\PHP_INT_MIN],
            'max' => [\PHP_INT_MAX],
        ]);

        it('creates from string', function (string $input, int $expected) {
            expect(IntegerPercent::fromString($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0', 0],
            'one' => ['1', 1],
            'fifty' => ['50', 50],
            'hundred' => ['100', 100],
        ]);

        it('creates from decimal string', function (string $input, int $expected) {
            expect(IntegerPercent::fromDecimal($input)->value())->toBe($expected);
        })->with([
            'zero' => ['0.0', 0],
            'one' => ['1.0', 1],
            'hundred' => ['100.0', 100],
        ]);

        it('throws when creating from invalid decimal string', function (string $input, string $exception) {
            expect(fn() => IntegerPercent::fromDecimal($input))->toThrow($exception);
        })->with([
            'negative' => ['-1.0', TypeException::class],
            'too large' => ['101.0', TypeException::class],
            'not a decimal' => ['50', DecimalTypeException::class],
            'leading zero' => ['050.0', DecimalTypeException::class],
            'plus sign' => ['+50.0', DecimalTypeException::class],
            'empty' => ['', DecimalTypeException::class],
            'whitespace' => [' 50.0 ', DecimalTypeException::class],
            'text' => ['abc', DecimalTypeException::class],
        ]);

        it('throws when creating from invalid string', function (string $input, string $exception) {
            expect(fn() => IntegerPercent::fromString($input))->toThrow($exception);
        })->with([
            'negative' => ['-1', IntegerTypeException::class],
            'too large' => ['101', IntegerTypeException::class],
            'float string' => ['50.0', StringTypeException::class],
            'leading zero' => ['050', StringTypeException::class],
            'plus sign' => ['+50', StringTypeException::class],
            'empty' => ['', StringTypeException::class],
            'whitespace' => [' 50 ', StringTypeException::class],
            'text' => ['abc', StringTypeException::class],
        ]);
    });

    describe('Try Factories', function () {
        it('tryFromBool returns instance', function (bool $input) {
            $result = IntegerPercent::tryFromBool($input);
            expect($result)->toBeInstanceOf(IntegerPercent::class)
                ->and($result->value())->toBe((int) $input);
        })->with([
            'true' => [true],
            'false' => [false],
        ]);

        it('tryFromFloat returns instance or default', function (float $input, bool $shouldFail) {
            $result = IntegerPercent::tryFromFloat($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPercent::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid zero' => [0.0, false],
            'valid hundred' => [100.0, false],
            'negative' => [-1.0, true],
            'too large' => [101.0, true],
            'invalid precision' => [1.5, true],
        ]);

        it('tryFromFloat returns custom default on failure', function () {
            $customDefault = Undefined::create();
            expect(IntegerPercent::tryFromFloat(-1.0, $customDefault))->toBe($customDefault);
        });

        it('tryFromInt returns instance or default', function (int $input, bool $shouldFail) {
            $result = IntegerPercent::tryFromInt($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPercent::class)
                    ->and($result->value())->toBe($input);
            }
        })->with([
            'zero' => [0, false],
            'fifty' => [50, false],
            'hundred' => [100, false],
            'negative' => [-1, true],
            'too large' => [101, true],
        ]);

        it('tryFromInt returns custom default on failure', function () {
            $customDefault = Undefined::create();
            expect(IntegerPercent::tryFromInt(-1, $customDefault))->toBe($customDefault);
        });

        it('tryFromString returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerPercent::tryFromString($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPercent::class)
                    ->and($result->value())->toBe((int) $input);
            }
        })->with([
            'valid' => ['50', false],
            'negative' => ['-1', true],
            'too large' => ['101', true],
            'invalid' => ['50.5', true],
        ]);

        it('tryFromString returns custom default on failure', function () {
            $customDefault = Undefined::create();
            expect(IntegerPercent::tryFromString('-1', $customDefault))->toBe($customDefault);
        });

        it('tryFromDecimal returns instance or default', function (string $input, bool $shouldFail) {
            $result = IntegerPercent::tryFromDecimal($input);
            if ($shouldFail) {
                expect($result)->toBeInstanceOf(Undefined::class);
            } else {
                expect($result)->toBeInstanceOf(IntegerPercent::class)
                    ->and($result->value())->toBe((int) (float) $input);
            }
        })->with([
            'valid' => ['50.0', false],
            'negative' => ['-1.0', true],
            'too large' => ['101.0', true],
            'invalid' => ['50', true],
        ]);

        it('tryFromDecimal returns custom default on failure', function () {
            $customDefault = Undefined::create();
            expect(IntegerPercent::tryFromDecimal('-1.0', $customDefault))->toBe($customDefault);
        });

        it('tryFromMixed returns instance for valid inputs', function (string $label, mixed $input, int $expected) {
            $result = IntegerPercent::tryFromMixed($input);
            expect($result)
                ->toBeInstanceOf(IntegerPercent::class, "Failed for [{$label}]: expected IntegerPercent instance")
                ->and($result->value())->toBe($expected, "Failed for [{$label}]: unexpected value");
        })->with([
            'int' => ['int', 50, 50],
            'float' => ['float', 50.0, 50],
            'bool true' => ['bool true', true, 1],
            'bool false' => ['bool false', false, 0],
            'string' => ['string', '50', 50],
            'instance' => ['instance', IntegerPercent::fromInt(50), 50],
            'stringable' => ['stringable', new class implements Stringable {
                public function __toString(): string
                {
                    return '50';
                }
            }, 50],
        ]);

        it('tryFromMixed returns default for invalid inputs', function (mixed $input) {
            expect(IntegerPercent::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'null' => [null],
            'array' => [[]],
            'negative int' => [-1],
            'too large int' => [101],
            'negative float' => [-1.0],
            'too large float' => [101.0],
            'invalid float' => [1.5],
            'invalid string' => ['abc'],
            'object' => [new stdClass()],
        ]);

        it('tryFromMixed returns custom default on failure', function () {
            $customDefault = Undefined::create();
            expect(IntegerPercent::tryFromMixed(null, $customDefault))->toBe($customDefault);
        });

        it('tryFromMixed returns default for instance with precision loss', function () {
            // Since IntegerPercent is 0-100, we can't have precision loss in the value itself,
            // but we can test tryFromMixed with an input that would fail internally.
            expect(IntegerPercent::tryFromMixed(1.5))->toBeInstanceOf(Undefined::class);
        });

        it('tryFromMixed handles resource/closure correctly', function (mixed $input) {
            expect(IntegerPercent::tryFromMixed($input))->toBeInstanceOf(Undefined::class);
        })->with([
            'resource' => [fn() => fopen('php://memory', 'r')],
            'closure' => [fn() => fn() => 50],
        ]);
    });

    describe('Converters', function () {
        it('converts to bool', function (int $input, bool $expected) {
            expect((new IntegerPercent($input))->toBool())->toBe($expected);
        })->with([
            'zero to false' => [0, false],
            'one to true' => [1, true],
            'fifty to true' => [50, true],
        ]);

        it('converts to float', function (int $input) {
            $v = new IntegerPercent($input);
            expect($v->toFloat())->toBe((float) $input);
        })->with([
            'zero' => [0],
            'fifty' => [50],
            'hundred' => [100],
        ]);

        it('converts to int', function (int $input) {
            expect((new IntegerPercent($input))->toInt())->toBe($input);
        })->with([0, 50, 100]);

        it('converts to string', function (int $input) {
            expect((new IntegerPercent($input))->toString())->toBe((string) $input)
                ->and((string) (new IntegerPercent($input)))->toBe((string) $input);
        })->with([0, 50, 100]);

        it('converts to decimal string', function (int $input, string $expected) {
            expect((new IntegerPercent($input))->toDecimal())->toBe($expected);
        })->with([
            'zero' => [0, '0.0'],
            'fifty' => [50, '50.0'],
            'hundred' => [100, '100.0'],
        ]);

        it('serializes to JSON', function (int $input) {
            expect((new IntegerPercent($input))->jsonSerialize())->toBe($input);
        })->with([0, 50, 100]);
    });

    describe('State checks', function () {
        it('is never empty', function () {
            expect((new IntegerPercent(0))->isEmpty())->toBeFalse();
        });

        it('is never undefined', function () {
            expect((new IntegerPercent(0))->isUndefined())->toBeFalse();
        });

        it('checks type correctly', function () {
            $v = new IntegerPercent(50);
            expect($v->isTypeOf(IntegerPercent::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass', IntegerPercent::class))->toBeTrue()
                ->and($v->isTypeOf('NotClass'))->toBeFalse();
        });

        it('fails isTypeOf with invalid classes', function () {
            $v = new IntegerPercent(50);
            expect($v->isTypeOf('UnknownClass', 'AnotherUnknown'))->toBeFalse();
        });
    });

    describe('Edge Cases', function () {
        it('handles boundary values', function (int $value) {
            $v = new IntegerPercent($value);
            expect($v->value())->toBe($value);
        })->with([
            'min' => [0],
            'max' => [100],
        ]);

        it('throws for values just outside boundaries', function (int $value) {
            expect(fn() => new IntegerPercent($value))->toThrow(IntegerTypeException::class);
        })->with([
            'below min' => [-1],
            'above max' => [101],
        ]);
    });
});
