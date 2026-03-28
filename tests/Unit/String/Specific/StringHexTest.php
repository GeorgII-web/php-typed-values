<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\HexStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringHex;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringHex', function () {
    it('accepts valid hex strings and preserves value', function (): void {
        $lower = new StringHex('4a6f686e');
        $upper = new StringHex('4A6F686E');
        $digits = new StringHex('0123456789');

        expect($lower->value())
            ->toBe('4a6f686e')
            ->and($upper->value())
            ->toBe('4A6F686E')
            ->and($digits->value())
            ->toBe('0123456789');
    });

    it('throws on invalid hex format', function (): void {
        // Invalid characters
        expect(fn() => new StringHex('not valid!'))
            ->toThrow(HexStringTypeException::class, 'Expected hexadecimal string, got "not valid!"');

        // Empty string
        expect(fn() => StringHex::fromString(''))
            ->toThrow(HexStringTypeException::class);

        // Spaces
        expect(fn() => StringHex::fromString('4a6f 686e'))
            ->toThrow(HexStringTypeException::class);
    });

    it('rejects strings with non-hex characters', function (string $invalidHex): void {
        expect(fn() => StringHex::fromString($invalidHex))
            ->toThrow(HexStringTypeException::class);
    })->with([
        'ghijkl',     // g-z are not hex
        '4a6f68!',    // ! is not hex
        '0x4a6f',     // x is not hex
        '4a6f 68',    // space
    ]);

    it('tryFromString returns instance for valid hex and Undefined for invalid', function (): void {
        $ok = StringHex::tryFromString('4a6f686e');
        $bad1 = StringHex::tryFromString('not valid!');
        $bad2 = StringHex::tryFromString('');

        expect($ok)
            ->toBeInstanceOf(StringHex::class)
            ->and($ok->value())
            ->toBe('4a6f686e')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid hex strings and invalid mixed inputs', function (): void {
        $ok = StringHex::tryFromMixed('4a6f686e');

        $stringable = new class {
            public function __toString(): string
            {
                return 'DEADBEEF';
            }
        };
        $fromStringable = StringHex::tryFromMixed($stringable);

        $badFormat = StringHex::tryFromMixed('not valid!');
        $fromArray = StringHex::tryFromMixed(['4a6f686e']);
        $fromNull = StringHex::tryFromMixed(null);
        $fromObject = StringHex::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringHex::class)
            ->and($ok->value())->toBe('4a6f686e')
            ->and($fromStringable)->toBeInstanceOf(StringHex::class)
            ->and($fromStringable->value())->toBe('DEADBEEF')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringHex::fromString('4a6f686e');
        $u1 = StringHex::tryFromString('not valid!');
        $u2 = StringHex::tryFromMixed(['hex']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('validates various valid hex strings', function (): void {
        $lower = StringHex::fromString('abcdef');
        $upper = StringHex::fromString('ABCDEF');
        $mixed = StringHex::fromString('aAbBcC');

        expect($lower->value())->toBe('abcdef')
            ->and($upper->value())->toBe('ABCDEF')
            ->and($mixed->value())->toBe('aAbBcC');
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect($v->isTypeOf(StringHex::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringHex', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect($v->jsonSerialize())->toBe('4a6f686e');
    });

    it('toString returns the hexadecimal string', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect($v->toString())->toBe('4a6f686e');
    });

    it('__toString returns the value', function (): void {
        $v = StringHex::fromString('4a6f686e');
        expect((string) $v)->toBe('4a6f686e');
    });

    it('tryFromMixed handles bool and float inputs', function (): void {
        $fromTrue = StringHex::tryFromMixed(true);
        $fromFalse = StringHex::tryFromMixed(false);
        $fromFloat = StringHex::tryFromMixed(1.5);

        expect($fromTrue)->toBeInstanceOf(Undefined::class)
            ->and($fromFalse)->toBeInstanceOf(Undefined::class)
            ->and($fromFloat)->toBeInstanceOf(Undefined::class);
    });

    it('covers conversions for StringHex', function (): void {
        expect(fn() => StringHex::fromBool(true))->toThrow(HexStringTypeException::class)
            ->and(fn() => StringHex::fromBool(false))->toThrow(HexStringTypeException::class)
            ->and(fn() => StringHex::fromFloat(1.2))->toThrow(HexStringTypeException::class)
            ->and(fn() => StringHex::fromDecimal('1.0'))->toThrow(HexStringTypeException::class);

        $fromInt = StringHex::fromInt(123);
        expect($fromInt)->toBeInstanceOf(StringHex::class)
            ->and($fromInt->value())->toBe('123');

        $v = StringHex::fromString('4a6f686e');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal for StringHex', function (): void {
        expect(StringHex::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringHex::tryFromBool(false))->toBeInstanceOf(Undefined::class)
            ->and(StringHex::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringHex::tryFromInt(123))->toBeInstanceOf(StringHex::class)
            ->and(StringHex::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
    });

    it('accepts numeric-only hex strings from tryFromMixed with int', function (): void {
        $fromInt = StringHex::tryFromMixed(123);
        expect($fromInt)->toBeInstanceOf(StringHex::class)
            ->and($fromInt->value())->toBe('123');
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringHexTest extends StringHex
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new Exception('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('test');
    }

    public static function fromString(string $value): static
    {
        throw new Exception('test');
    }
}

describe('Throwing static', function () {
    it('StringHex::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringHexTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringHexTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringHexTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringHexTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringHexTest::tryFromMixed('4a6f686e'))->toBeInstanceOf(Undefined::class)
            ->and(StringHexTest::tryFromString('4a6f686e'))->toBeInstanceOf(Undefined::class);
    });
});
