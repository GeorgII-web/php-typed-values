<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\Bool\Specific;

use PhpTypedValues\Bool\Specific\BoolToggle;
use PhpTypedValues\Exception\Bool\ToggleBoolTypeException;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(BoolToggle::class);

describe('BoolToggle - Instantiation and Core Methods', function (): void {
    it('constructs with boolean values', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->value())->toBe($value);
    })->with([true, false]);

    it('provides correct string representation', function (bool $value, string $expectedString): void {
        $bool = new BoolToggle($value);
        expect($bool->toString())->toBe($expectedString)
            ->and((string) $bool)->toBe($expectedString)
            ->and($bool->__toString())->toBe($expectedString);
    })->with([
        [true, 'true'],
        [false, 'false'],
    ]);

    it('provides correct label representation', function (bool $value, string $expectedLabel): void {
        $bool = new BoolToggle($value);
        expect($bool->toLabel())->toBe($expectedLabel);
    })->with([
        [true, 'yes'],
        [false, 'no'],
    ]);

    it('jsonSerialize returns native boolean', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->jsonSerialize())->toBe($value);
    })->with([true, false]);

    it('isEmpty always returns false', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->isEmpty())->toBeFalse();
    })->with([true, false]);

    it('isUndefined always returns false', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->isUndefined())->toBeFalse();
    })->with([true, false]);

    it('isTypeOf returns true when class matches', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->isTypeOf(BoolToggle::class))->toBeTrue();
    })->with([true, false]);

    it('isTypeOf returns false when class does not match', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->isTypeOf('NonExistentClass'))->toBeFalse();
    })->with([true, false]);

    it('isTypeOf returns true for multiple classNames when one matches', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->isTypeOf('NonExistentClass', BoolToggle::class, 'AnotherClass'))->toBeTrue();
    })->with([true, false]);

    it('toBool returns boolean value', function (bool $value): void {
        $bool = new BoolToggle($value);
        expect($bool->toBool())->toBe($value);
    })->with([true, false]);

    it('toInt returns integer representation', function (bool $value, int $expectedInt): void {
        $bool = new BoolToggle($value);
        expect($bool->toInt())->toBe($expectedInt);
    })->with([
        [true, 1],
        [false, 0],
    ]);

    it('toFloat returns float representation', function (bool $value, float $expectedFloat): void {
        $bool = new BoolToggle($value);
        expect($bool->toFloat())->toBe($expectedFloat);
    })->with([
        [true, 1.0],
        [false, 0.0],
    ]);

    it('toDecimal returns decimal representation', function (bool $value, string $expectedDecimal): void {
        $bool = new BoolToggle($value);
        expect($bool->toDecimal())->toBe($expectedDecimal);
    })->with([
        [true, '1.0'],
        [false, '0.0'],
    ]);
});

describe('BoolToggle - from* Factory Methods', function (): void {
    it('fromBool creates instance from boolean', function (bool $value): void {
        $bool = BoolToggle::fromBool($value);
        expect($bool->value())->toBe($value);
    })->with([true, false]);

    it('fromInt creates instance from valid integers', function (int $input, bool $expected): void {
        $bool = BoolToggle::fromInt($input);
        expect($bool->value())->toBe($expected);
    })->with([
        [1, true],
        [0, false],
    ]);

    it('fromDecimal creates instance from valid decimals', function (string $input, bool $expected): void {
        $bool = BoolToggle::fromDecimal($input);
        expect($bool->value())->toBe($expected);
    })->with([
        ['1.0', true],
        ['0.0', false],
    ]);

    it('fromInt throws IntegerTypeException for invalid integers', function (int $invalidValue): void {
        expect(fn() => BoolToggle::fromInt($invalidValue))
            ->toThrow(IntegerTypeException::class);
    })->with([-1, 2, 10, -10]);

    it('fromFloat creates instance from valid floats', function (float $input, bool $expected): void {
        $bool = BoolToggle::fromFloat($input);
        expect($bool->value())->toBe($expected);
    })->with([
        [1.0, true],
        [0.0, false],
    ]);

    it('fromFloat throws FloatTypeException for invalid floats', function (float $invalidValue): void {
        expect(fn() => BoolToggle::fromFloat($invalidValue))
            ->toThrow(FloatTypeException::class);
    })->with([-1.0, 2.0, 0.5, -0.5, 1.1]);

    it('fromString creates instance from valid strings', function (string $input, bool $expected): void {
        $bool = BoolToggle::fromString($input);
        expect($bool->value())->toBe($expected);
    })->with([
        ['true', true],
        ['false', false],
    ]);

    it('fromString throws StringTypeException for invalid strings', function (string $invalidValue): void {
        expect(fn() => BoolToggle::fromString($invalidValue))
            ->toThrow(StringTypeException::class);
    })->with([
        ['yes'],
        ['no'],
        ['on'],
        ['off'],
        ['1'],
        ['0'],
        ['invalid'],
        ['TRUE'],
    ]);

    it('fromLabel creates instance from valid labels', function (string $input, bool $expected): void {
        $bool = BoolToggle::fromLabel($input);
        expect($bool->value())->toBe($expected);
    })->with([
        ['yes', true],
        ['no', false],
    ]);

    it('fromLabel throws ToggleBoolTypeException for invalid strings', function (string $invalidValue): void {
        expect(fn() => BoolToggle::fromLabel($invalidValue))
            ->toThrow(ToggleBoolTypeException::class);
    })->with([
        ['true'],
        ['false'],
        ['on'],
        ['off'],
        ['1'],
        ['0'],
        ['invalid'],
        ['TRUE'],
    ]);
});

describe('BoolToggle - tryFrom* Methods', function (): void {
    it('tryFromBool returns BoolToggle for boolean values', function (bool $value): void {
        $result = BoolToggle::tryFromBool($value);
        expect($result)->toBeInstanceOf(BoolToggle::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('tryFromInt returns appropriate result', function (int $input, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromInt($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
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

    it('tryFromFloat returns appropriate result', function (float $input, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromFloat($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
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

    it('tryFromString returns appropriate result', function (string $input, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromString($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        ['true', true],
        ['false', false],
        ['TRUE', Undefined::class],
        ['yes', Undefined::class],
        ['no', Undefined::class],
        ['invalid', Undefined::class],
    ]);

    it('tryFromLabel returns appropriate result', function (string $input, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromLabel($input);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        ['yes', true],
        ['no', false],
        ['YES', Undefined::class],
        ['true', Undefined::class],
        ['false', Undefined::class],
        ['invalid', Undefined::class],
    ]);
});

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolToggleTest extends BoolToggle
{
    public function __toString(): string
    {
        return 'not-a-boolean';
    }
}

describe('BoolToggle - tryFromMixed Method', function (): void {
    it('handles boolean inputs', function (bool $value): void {
        $result = BoolToggle::tryFromMixed($value);
        expect($result)->toBeInstanceOf(BoolToggle::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('handles integer inputs', function (int $value, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1, true],
        [0, false],
        [-1, Undefined::class],
        [2, Undefined::class],
    ]);

    it('handles float inputs', function (float $value, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        [1.0, true],
        [0.0, false],
        [-1.0, Undefined::class],
        [2.0, Undefined::class],
    ]);

    it('handles string inputs including standard bool and labels', function (string $value, bool|string $expectedResult): void {
        $result = BoolToggle::tryFromMixed($value);

        if ($expectedResult === true) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeTrue();
        } elseif ($expectedResult === false) {
            expect($result)->toBeInstanceOf(BoolToggle::class)
                ->and($result->value())->toBeFalse();
        } else {
            expect($result)->toBeInstanceOf($expectedResult);
        }
    })->with([
        ['true', true],
        ['false', false],
        ['yes', true],
        ['no', false],
        ['on', Undefined::class],
        ['off', Undefined::class],
        ['TRUE', Undefined::class],
        ['YES', Undefined::class],
        ['', Undefined::class],
    ]);

    it('handles Stringable objects', function (): void {
        $stringableTrue = new class implements Stringable {
            public function __toString(): string
            {
                return 'true';
            }
        };

        $stringableNo = new class implements Stringable {
            public function __toString(): string
            {
                return 'no';
            }
        };

        $stringableInvalid = new class implements Stringable {
            public function __toString(): string
            {
                return 'invalid';
            }
        };

        expect(BoolToggle::tryFromMixed($stringableTrue))
            ->toBeInstanceOf(BoolToggle::class)
            ->and(BoolToggle::tryFromMixed($stringableTrue)->value())->toBeTrue()
            ->and(BoolToggle::tryFromMixed($stringableNo))
            ->toBeInstanceOf(BoolToggle::class)
            ->and(BoolToggle::tryFromMixed($stringableNo)->value())->toBeFalse()
            ->and(BoolToggle::tryFromMixed($stringableInvalid))
            ->toBeInstanceOf(Undefined::class);
    });

    it('handles existing BoolToggle instances', function (bool $value): void {
        $bool = new BoolToggle($value);
        $result = BoolToggle::tryFromMixed($bool);

        expect($result)->toBeInstanceOf(BoolToggle::class)
            ->and($result->value())->toBe($value);
    })->with([true, false]);

    it('kills InstanceOfToFalse mutant in tryFromMixed', function (): void {
        $subclass = new BoolToggleTest(true);

        $result = BoolToggle::tryFromMixed($subclass);
        expect($result)->toBeInstanceOf(BoolToggle::class)
            ->and($result->value())->toBeTrue();
    });

    it('returns Undefined for unsupported types', function (mixed $invalidValue): void {
        $result = BoolToggle::tryFromMixed($invalidValue);
        expect($result)->toBeInstanceOf(Undefined::class);
    })->with([
        [null],
        [[]],
        [['x']],
        [new stdClass()],
        [fn() => null],
    ]);

    it('handles Exception in default match case', function (): void {
        $resource = fopen('php://memory', 'r');
        fclose($resource);

        $result = BoolToggle::tryFromMixed($resource);
        expect($result)->toBeInstanceOf(Undefined::class);
    });
});
