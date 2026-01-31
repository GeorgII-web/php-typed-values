<?php

declare(strict_types=1);

use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(BoolStandard::class);

describe('BoolStandard - Instantiation and Core Methods', function (): void {
    it('constructs with boolean values', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->value())->toBe($value);
    })->with([true, false]);

    it('provides correct string representation', function (bool $value, string $expectedString): void {
        $bool = new BoolStandard($value);
        expect($bool->toString())->toBe($expectedString)
            ->and((string) $bool)->toBe($expectedString)
            ->and($bool->__toString())->toBe($expectedString);
    })->with([
        [true, 'true'],
        [false, 'false'],
    ]);

    it('jsonSerialize returns native boolean', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->jsonSerialize())->toBe($value);
    })->with([true, false]);

    it('isEmpty always returns false', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->isEmpty())->toBeFalse();
    })->with([true, false]);

    it('isUndefined always returns false', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->isUndefined())->toBeFalse();
    })->with([true, false]);

    it('isTypeOf returns true when class matches', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->isTypeOf(BoolStandard::class))->toBeTrue();
    })->with([true, false]);

    it('isTypeOf returns false when class does not match', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->isTypeOf('NonExistentClass'))->toBeFalse();
    })->with([true, false]);

    it('isTypeOf returns true for multiple classNames when one matches', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->isTypeOf('NonExistentClass', BoolStandard::class, 'AnotherClass'))->toBeTrue();
    })->with([true, false]);

    it('toBool returns boolean value', function (bool $value): void {
        $bool = new BoolStandard($value);
        expect($bool->toBool())->toBe($value);
    })->with([true, false]);

    it('toInt returns integer representation', function (bool $value, int $expectedInt): void {
        $bool = new BoolStandard($value);
        expect($bool->toInt())->toBe($expectedInt);
    })->with([
        [true, 1],
        [false, 0],
    ]);

    it('toFloat returns float representation', function (bool $value, float $expectedFloat): void {
        $bool = new BoolStandard($value);
        expect($bool->toFloat())->toBe($expectedFloat);
    })->with([
        [true, 1.0],
        [false, 0.0],
    ]);
});

describe('BoolStandard - from* Factory Methods', function (): void {
    it('fromBool creates instance from boolean', function (bool $value): void {
        $bool = BoolStandard::fromBool($value);
        expect($bool->value())->toBe($value);
    })->with([true, false]);

    it('fromInt creates instance from valid integers', function (int $input, bool $expected): void {
        $bool = BoolStandard::fromInt($input);
        expect($bool->value())->toBe($expected);
    })->with([
        [1, true],
        [0, false],
    ]);

    it('fromInt throws IntegerTypeException for invalid integers', function (int $invalidValue): void {
        expect(fn() => BoolStandard::fromInt($invalidValue))
            ->toThrow(IntegerTypeException::class);
    })->with([-1, 2, 10, -10]);

    it('fromFloat creates instance from valid floats', function (float $input, bool $expected): void {
        $bool = BoolStandard::fromFloat($input);
        expect($bool->value())->toBe($expected);
    })->with([
        [1.0, true],
        [0.0, false],
    ]);

    it('fromFloat throws FloatTypeException for invalid floats', function (float $invalidValue): void {
        expect(fn() => BoolStandard::fromFloat($invalidValue))
            ->toThrow(FloatTypeException::class);
    })->with([-1.0, 2.0, 0.5, -0.5, 1.1]);

    it('fromString creates instance from valid strings', function (string $input, bool $expected): void {
        $bool = BoolStandard::fromString($input);
        expect($bool->value())->toBe($expected);
    })->with([
        // Case-sensitive lowercase only
        ['true', true],
        ['false', false],
    ]);

    it('fromString throws StringTypeException for invalid strings', function (string $invalidValue, ?string $expectedExceptionMessage = null): void {
        $test = fn() => BoolStandard::fromString($invalidValue);

        if ($expectedExceptionMessage) {
            expect($test)->toThrow(StringTypeException::class, $expectedExceptionMessage);
        } else {
            expect($test)->toThrow(StringTypeException::class);
        }
    })->with([
        // Uppercase variations
        ['TRUE', 'String "TRUE" has no valid strict bool value'],
        ['True', 'String "True" has no valid strict bool value'],
        ['FALSE', 'String "FALSE" has no valid strict bool value'],
        ['False', 'String "False" has no valid strict bool value'],

        // Other invalid values
        ['yes', 'String "yes" has no valid strict bool value'],
        ['no', 'String "no" has no valid strict bool value'],
        ['on', 'String "on" has no valid strict bool value'],
        ['off', 'String "off" has no valid strict bool value'],
        ['1', 'String "1" has no valid strict bool value'],
        ['0', 'String "0" has no valid strict bool value'],
        ['', 'String "" has no valid strict bool value'],
        [' ', 'String " " has no valid strict bool value'],
        ['invalid', 'String "invalid" has no valid strict bool value'],
        ['true ', 'String "true " has no valid strict bool value'],
        [' true', 'String " true" has no valid strict bool value'],
    ]);
});

describe('BoolStandard - tryFrom* Methods', function (): void {
    it('tryFromBool returns BoolStandard for boolean values', function (bool $value): void {
        $result = BoolStandard::tryFromBool($value);
        expect($result)->toBeInstanceOf(BoolStandard::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('tryFromInt returns appropriate result', function (int $input, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromInt($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1, true],
        [0, false],
        [-1, Undefined::class],
        [2, Undefined::class],
        [10, Undefined::class],
    ]);

    it('tryFromFloat returns appropriate result', function (float $input, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromFloat($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1.0, true],
        [0.0, false],
        [-1.0, Undefined::class],
        [2.0, Undefined::class],
        [0.5, Undefined::class],
        [1.1, Undefined::class],
    ]);

    it('tryFromString returns appropriate result', function (string $input, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromString($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        // Valid lowercase strings
        ['true', true],
        ['false', false],

        // Invalid strings (case-sensitive)
        ['TRUE', Undefined::class],
        ['True', Undefined::class],
        ['FALSE', Undefined::class],
        ['False', Undefined::class],
        ['yes', Undefined::class],
        ['no', Undefined::class],
        ['on', Undefined::class],
        ['off', Undefined::class],
        ['1', Undefined::class],
        ['0', Undefined::class],
        ['', Undefined::class],
        [' ', Undefined::class],
        ['invalid', Undefined::class],
        ['true ', Undefined::class],
        [' true', Undefined::class],
    ]);
});

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolStandardTest extends BoolStandard
{
    public function __toString(): string
    {
        return 'not-a-boolean';
    }
}

describe('BoolStandard - tryFromMixed Method', function (): void {
    it('handles boolean inputs', function (bool $value): void {
        $result = BoolStandard::tryFromMixed($value);
        expect($result)->toBeInstanceOf(BoolStandard::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('handles integer inputs', function (int $value, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1, true],
        [0, false],
        [-1, Undefined::class],
        [2, Undefined::class],
        [10, Undefined::class],
    ]);

    it('handles float inputs', function (float $value, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1.0, true],
        [0.0, false],
        [-1.0, Undefined::class],
        [2.0, Undefined::class],
        [0.5, Undefined::class],
        [1.1, Undefined::class],
    ]);

    it('handles string inputs', function (string $value, string|bool $expectedResult): void {
        $result = BoolStandard::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolStandard::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        ['true', true],
        ['false', false],
        ['TRUE', Undefined::class],
        ['FALSE', Undefined::class],
        ['yes', Undefined::class],
        ['no', Undefined::class],
        ['on', Undefined::class],
        ['off', Undefined::class],
        ['', Undefined::class],
        [' ', Undefined::class],
    ]);

    it('handles Stringable objects', function (): void {
        $stringableTrue = new class implements Stringable {
            public function __toString(): string
            {
                return 'true';
            }
        };

        $stringableFalse = new class implements Stringable {
            public function __toString(): string
            {
                return 'false';
            }
        };

        $stringableInvalid = new class implements Stringable {
            public function __toString(): string
            {
                return 'invalid';
            }
        };

        $stringableUppercase = new class implements Stringable {
            public function __toString(): string
            {
                return 'TRUE';
            }
        };

        expect(BoolStandard::tryFromMixed($stringableTrue))
            ->toBeInstanceOf(BoolStandard::class)
            ->and(BoolStandard::tryFromMixed($stringableTrue)->value())->toBeTrue()
            ->and(BoolStandard::tryFromMixed($stringableFalse))
            ->toBeInstanceOf(BoolStandard::class)
            ->and(BoolStandard::tryFromMixed($stringableFalse)->value())->toBeFalse()
            ->and(BoolStandard::tryFromMixed($stringableInvalid))
            ->toBeInstanceOf(Undefined::class)
            ->and(BoolStandard::tryFromMixed($stringableUppercase))
            ->toBeInstanceOf(Undefined::class);
    });

    it('handles existing BoolStandard instances', function (bool $value): void {
        $bool = new BoolStandard($value);
        $result = BoolStandard::tryFromMixed($bool);

        expect($result)->toBeInstanceOf(BoolStandard::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('kills InstanceOfToFalse mutant in tryFromMixed', function (): void {
        $subclass = new BoolStandardTest(true);

        // If mutant replaces ($value instanceof self) with false,
        // it will fall through to Stringable check and call fromString('not-a-boolean'),
        // which throws IntegerTypeException, and tryFromMixed will catch it and return Undefined.
        // If it correctly uses ($value instanceof self), it will call fromBool(true) and succeed.
        $result = BoolStandard::tryFromMixed($subclass);
        expect($result)->toBeInstanceOf(BoolStandard::class)
            ->and($result->value())->toBeTrue();
    });

    it('returns Undefined for unsupported types', function (mixed $invalidValue): void {
        $result = BoolStandard::tryFromMixed($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([
        [null],
        [[]],
        [['x']],
        [new stdClass()],
        [fn() => null], // callable
        [new class {}], // anonymous object without Stringable
    ]);

    it('handles TypeException in default match case', function (): void {
        // This tests the default case in the match statement
        // We need to pass something that doesn't match any condition
        $resource = fopen('php://memory', 'r');
        fclose($resource);

        $result = BoolStandard::tryFromMixed($resource);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
