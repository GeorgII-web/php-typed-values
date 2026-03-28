<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\String\CreditCardStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringCreditCard;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringCreditCard', function () {
    it('accepts valid credit card numbers and preserves digits', function (): void {
        $visa13 = new StringCreditCard('4111111111119'); // 13 digits, corrected Luhn
        $mc = new StringCreditCard('5500000000000004'); // 16 digits
        $jcb19 = new StringCreditCard('3530111333300000001'); // 19 digits, corrected Luhn

        expect($visa13->value())
            ->toBe('4111111111119')
            ->and($mc->value())
            ->toBe('5500000000000004')
            ->and($jcb19->value())
            ->toBe('3530111333300000001');
    });

    it('strips spaces and dashes before validation', function (): void {
        $spaced = new StringCreditCard('4111 1111 1111 1111');
        $dashed = new StringCreditCard('4111-1111-1111-1111');

        expect($spaced->value())
            ->toBe('4111111111111111')
            ->and($dashed->value())
            ->toBe('4111111111111111');
    });

    it('throws on invalid credit card numbers', function (): void {
        expect(fn() => new StringCreditCard('1234567890123456'))
            ->toThrow(CreditCardStringTypeException::class, 'Expected valid credit card number, got "1234567890123456"');

        expect(fn() => StringCreditCard::fromString(''))
            ->toThrow(CreditCardStringTypeException::class);

        expect(fn() => StringCreditCard::fromString('abc'))
            ->toThrow(CreditCardStringTypeException::class);

        expect(fn() => StringCreditCard::fromString('411111111117')) // 12 digits, valid Luhn
            ->toThrow(CreditCardStringTypeException::class);

        expect(fn() => StringCreditCard::fromString('11111111111111111111')) // 20 digits, valid Luhn
            ->toThrow(CreditCardStringTypeException::class);

        expect(fn() => new StringCreditCard('424242424242444a')) // Passes Luhn but contains non-digit
            ->toThrow(CreditCardStringTypeException::class);

        expect(fn() => new StringCreditCard('42424242424244')) // Length 14, invalid digits
            ->toThrow(CreditCardStringTypeException::class);
    });

    it('rejects numbers that are too short or too long', function (string $invalid): void {
        expect(fn() => StringCreditCard::fromString($invalid))
            ->toThrow(CreditCardStringTypeException::class);
    })->with([
        '41111111111',           // 11 digits - too short
        '411111111117',          // 12 digits - too short, but valid Luhn
        '11111111111111111111',  // 20 digits - too long, but valid Luhn
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringCreditCard::tryFromString('4111111111111111');
        $bad1 = StringCreditCard::tryFromString('1234567890123456');
        $bad2 = StringCreditCard::tryFromString('');

        expect($ok)
            ->toBeInstanceOf(StringCreditCard::class)
            ->and($ok->value())
            ->toBe('4111111111111111')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid strings and invalid mixed inputs', function (): void {
        $ok = StringCreditCard::tryFromMixed('4111111111111111');

        $stringable = new class {
            public function __toString(): string
            {
                return '4111111111111111';
            }
        };
        $fromStringable = StringCreditCard::tryFromMixed($stringable);

        $badFormat = StringCreditCard::tryFromMixed('not valid!');
        $fromArray = StringCreditCard::tryFromMixed(['4111111111111111']);
        $fromNull = StringCreditCard::tryFromMixed(null);
        $fromScalar = StringCreditCard::tryFromMixed(123);
        $fromObject = StringCreditCard::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringCreditCard::class)
            ->and($ok->value())->toBe('4111111111111111')
            ->and($fromStringable)->toBeInstanceOf(StringCreditCard::class)
            ->and($fromStringable->value())->toBe('4111111111111111')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringCreditCard::fromString('4111111111111111');
        $u1 = StringCreditCard::tryFromString('not valid!');
        $u2 = StringCreditCard::tryFromMixed(['card']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('validates various valid credit card numbers', function (string $card): void {
        $instance = StringCreditCard::fromString($card);
        expect($instance->value())->toBe($card);
    })->with([
        '4111111111119',     // 13 digits, corrected Luhn
        '4111111111111111',  // 16 digits (Visa)
        '5500000000000004',  // Mastercard
        '378282246310005',   // Amex
        '6011111111111117',  // Discover
        '3530111333300000',  // JCB (16)
        '3530111333300000001', // 19 digits, corrected Luhn
    ]);

    it('validates Luhn edge cases', function (): void {
        // '5500000000000004': length 16, parity 0.
        // i=0: 5 -> 5*2 = 10, 10 > 9 -> 10 - 9 = 1.
        $instance = StringCreditCard::fromString('5500000000000004');
        expect($instance->value())->toBe('5500000000000004');

        // '0049927398716'. length 13. parity 1.
        // i=3: 9 (3 % 2 == 1) -> 9*2 = 18, 18 > 9 -> 18 - 9 = 9.
        $instance2 = StringCreditCard::fromString('0049927398716');
        expect($instance2->value())->toBe('0049927398716');
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect($v->isTypeOf(StringCreditCard::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringCreditCard', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect($v->jsonSerialize())->toBe('4111111111111111');
    });

    it('toString returns the credit card number string', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect($v->toString())->toBe('4111111111111111');
    });

    it('__toString returns the value', function (): void {
        $v = StringCreditCard::fromString('4111111111111111');
        expect((string) $v)->toBe('4111111111111111');
    });

    it('tryFromMixed handles bool and float inputs', function (): void {
        $fromTrue = StringCreditCard::tryFromMixed(true);
        $fromFalse = StringCreditCard::tryFromMixed(false);
        $fromFloat = StringCreditCard::tryFromMixed(1.5);

        expect($fromTrue)->toBeInstanceOf(Undefined::class)
            ->and($fromFalse)->toBeInstanceOf(Undefined::class)
            ->and($fromFloat)->toBeInstanceOf(Undefined::class);
    });

    it('covers conversions for StringCreditCard', function (): void {
        expect(fn() => StringCreditCard::fromBool(true))->toThrow(CreditCardStringTypeException::class)
            ->and(fn() => StringCreditCard::fromBool(false))->toThrow(CreditCardStringTypeException::class)
            ->and(fn() => StringCreditCard::fromFloat(1.2))->toThrow(CreditCardStringTypeException::class)
            ->and(fn() => StringCreditCard::fromInt(123))->toThrow(CreditCardStringTypeException::class)
            ->and(fn() => StringCreditCard::fromDecimal('1.0'))->toThrow(CreditCardStringTypeException::class);

        $v = StringCreditCard::fromString('4111111111111111');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and($v->toInt())->toBe(4111111111111111)
            ->and($v->toDecimal())->toBe('4111111111111111');
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal for StringCreditCard', function (): void {
        expect(StringCreditCard::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCard::tryFromBool(false))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCard::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCard::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCard::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringCreditCardTest extends StringCreditCard
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
    it('StringCreditCard::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringCreditCardTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCardTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCardTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCardTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCardTest::tryFromMixed('4111111111111111'))->toBeInstanceOf(Undefined::class)
            ->and(StringCreditCardTest::tryFromString('4111111111111111'))->toBeInstanceOf(Undefined::class);
    });
});
