<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\LocaleStringTypeException;
use PhpTypedValues\String\Specific\StringLocaleCode;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(StringLocaleCode::class);

describe('StringLocaleCode', function () {
    it('constructs from a valid locale code', function (): void {
        $l = new StringLocaleCode('en_US');

        expect($l->value())->toBe('en_US')
            ->and($l->toString())->toBe('en_US')
            ->and((string) $l)->toBe('en_US')
            ->and($l->getLanguageCode())->toBe('en')
            ->and($l->getCountryCode())->toBe('US')
            ->and($l->isEmpty())->toBeFalse()
            ->and($l->isUndefined())->toBeFalse();
    });

    it('throws exception for invalid format', function (string $invalid): void {
        expect(fn() => new StringLocaleCode($invalid))
            ->toThrow(LocaleStringTypeException::class, 'Expected locale code (ll_RR), got "' . $invalid . '"');
    })->with([
        'en-US',
        'en_USA',
        'EN_US',
        'en_us',
        'e_US',
        'en_U',
        '12_34',
    ]);

    it('throws exception for unknown language code', function (): void {
        expect(fn() => new StringLocaleCode('zz_US'))
            ->toThrow(LocaleStringTypeException::class, 'Unknown ISO 639-1 language code "zz"');
    });

    it('throws exception for unknown country code', function (): void {
        expect(fn() => new StringLocaleCode('en_ZZ'))
            ->toThrow(LocaleStringTypeException::class, 'Unknown ISO 3166-1 country code "ZZ"');
    });

    it('fromString constructs from a valid locale code', function (): void {
        $l = StringLocaleCode::fromString('de_DE');
        expect($l->value())->toBe('de_DE');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        expect(StringLocaleCode::tryFromString('fr_FR'))->toBeInstanceOf(StringLocaleCode::class)
            ->and(StringLocaleCode::tryFromString('invalid'))->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns the locale string', function (): void {
        $l = new StringLocaleCode('it_IT');
        expect($l->jsonSerialize())->toBe('it_IT');
    });

    it('isTypeOf returns true when class matches', function (): void {
        $l = StringLocaleCode::fromString('en_GB');
        expect($l->isTypeOf(StringLocaleCode::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $l = StringLocaleCode::fromString('en_GB');
        expect($l->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $l = StringLocaleCode::fromString('en_GB');
        expect($l->isTypeOf('NonExistentClass', StringLocaleCode::class, 'AnotherClass'))->toBeTrue();
    });

    it('conversion from other types (that happen to be valid locale codes)', function (): void {
        // This is unlikely to happen in practice for float/bool/int, but we must test the methods.
        // However, none of 'true', 'false', '1', '1.0' match 'll_RR'.
        // So they should all throw.

        expect(fn() => StringLocaleCode::fromBool(true))->toThrow(LocaleStringTypeException::class)
            ->and(fn() => StringLocaleCode::fromFloat(1.0))->toThrow(LocaleStringTypeException::class)
            ->and(fn() => StringLocaleCode::fromInt(123))->toThrow(LocaleStringTypeException::class);
    });

    it('conversions to other types', function (): void {
        $l = new StringLocaleCode('en_US');
        // 'en_US' is not a valid bool, float, or int string.
        expect(fn() => $l->toBool())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $l->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $l->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return Undefined', function (): void {
        expect(StringLocaleCode::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCode::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCode::tryFromInt(1))->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles various inputs', function (): void {
        $valid = 'en_US';
        $instance = new StringLocaleCode($valid);

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'de_DE';
            }
        };

        expect(StringLocaleCode::tryFromMixed($valid))->toBeInstanceOf(StringLocaleCode::class)
            ->and(StringLocaleCode::tryFromMixed($valid)->value())->toBe($valid)
            ->and(StringLocaleCode::tryFromMixed($instance))->toBeInstanceOf(StringLocaleCode::class)
            ->and(StringLocaleCode::tryFromMixed($instance)->value())->toBe($valid)
            ->and(StringLocaleCode::tryFromMixed($stringable))->toBeInstanceOf(StringLocaleCode::class)
            ->and(StringLocaleCode::tryFromMixed($stringable)->value())->toBe('de_DE')
            ->and(StringLocaleCode::tryFromMixed(null))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCode::tryFromMixed([]))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCode::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringLocaleCodeTest extends StringLocaleCode
{
    public function __construct(string $value)
    {
        throw new Exception('test');
    }
}

describe('Throwing static', function () {
    it('tryFrom* methods return Undefined when exception occurs (coverage)', function (): void {
        // We need to trigger the catch blocks in tryFrom* methods.
        // Since StringLocaleCode uses 'new static', we can use a throwing subclass.

        expect(StringLocaleCodeTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCodeTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCodeTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCodeTest::tryFromMixed('en_US'))->toBeInstanceOf(Undefined::class)
            ->and(StringLocaleCodeTest::tryFromString('en_US'))->toBeInstanceOf(Undefined::class);
    });
});
