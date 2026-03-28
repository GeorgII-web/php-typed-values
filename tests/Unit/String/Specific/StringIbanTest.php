<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\IbanStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringIban;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringIban', function () {
    it('accepts valid IBAN strings and preserves normalized value', function (string $input, string $expected): void {
        $iban = new StringIban($input);
        expect($iban->value())->toBe($expected);
    })->with([
        ['DE89 3704 0044 0532 0130 00', 'DE89370400440532013000'],
        ['de89370400440532013000', 'DE89370400440532013000'],
        [' GB 29 NWBK 6016 1331 9268 19 ', 'GB29NWBK60161331926819'],
    ]);

    it('throws on invalid IBAN format', function (string $invalid): void {
        expect(fn() => new StringIban($invalid))
            ->toThrow(IbanStringTypeException::class);
    })->with([
        '',
        'NOT AN IBAN',
        'DE89 3704 0044 0532 0130 01', // Invalid checksum
        'ABC', // Too short
        'DE89 3704 0044 0532 0130 00 extra', // Too long (if it exceeds 34)
        '1234 5678 9012 3456 7890 12', // Doesn't start with country code
    ]);

    it('tryFromString returns instance for valid IBAN and Undefined for invalid', function (): void {
        $ok = StringIban::tryFromString('DE89 3704 0044 0532 0130 00');
        $bad = StringIban::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringIban::class)
            ->and($ok->value())
            ->toBe('DE89370400440532013000')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid IBAN strings and invalid mixed inputs', function (): void {
        $ok = StringIban::tryFromMixed('DE89 3704 0044 0532 0130 00');

        $stringable = new class {
            public function __toString(): string
            {
                return 'DE89 3704 0044 0532 0130 00';
            }
        };
        $fromStringable = StringIban::tryFromMixed($stringable);

        $badFormat = StringIban::tryFromMixed('not valid!');
        $fromArray = StringIban::tryFromMixed(['DE89']);
        $fromNull = StringIban::tryFromMixed(null);
        $fromObject = StringIban::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringIban::class)
            ->and($ok->value())->toBe('DE89370400440532013000')
            ->and($fromStringable)->toBeInstanceOf(StringIban::class)
            ->and($fromStringable->value())->toBe('DE89370400440532013000')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        $u1 = StringIban::tryFromString('not valid!');

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue();
    });

    it('isUndefined returns false (explicit call)', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->isUndefined())->toBeFalse();
    });

    it('rejects IBAN with non-numeric characters in moved part after translation', function (): void {
        // IBAN: "DE89 3704 0044 0532 0130 00" -> moved "3704 0044 0532 0130 00 DE 89"
        // Let's craft an IBAN that has a non-alphanumeric character but passes previous checks
        // isValidIban check 1: len < 4 || len > 34
        // isValidIban check 2: !ctype_alpha(substr($iban, 0, 2)) || !ctype_digit(substr($iban, 2, 2))
        // So it must start with 2 letters and 2 digits.
        // If it contains a special character later, ctype_digit($numeric) will be false.

        expect(fn() => new StringIban('DE89!70400440532013000'))
            ->toThrow(IbanStringTypeException::class);
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->isTypeOf(StringIban::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringIban', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->jsonSerialize())->toBe('DE89370400440532013000');
    });

    it('toString returns the normalized IBAN string', function (): void {
        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect($v->toString())->toBe('DE89370400440532013000');
    });

    it('covers conversions for StringIban', function (): void {
        expect(fn() => StringIban::fromBool(true))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromFloat(1.2))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromInt(123))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromDecimal('1.0'))->toThrow(IbanStringTypeException::class);

        $v = StringIban::fromString('DE89 3704 0044 0532 0130 00');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringIbanTest extends StringIban
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
    it('StringIban::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringIbanTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromMixed('DE89 3704 0044 0532 0130 00'))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromString('DE89 3704 0044 0532 0130 00'))->toBeInstanceOf(Undefined::class);
    });
});
