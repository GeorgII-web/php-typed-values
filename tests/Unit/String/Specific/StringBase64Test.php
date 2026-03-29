<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\Base64StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringBase64;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringBase64', function () {
    it('accepts valid Base64 strings and preserves value', function (): void {
        $simple = new StringBase64('SGVsbG8gV29ybGQ=');
        $noPad = new StringBase64('SGVsbG8h');
        $twoPad = new StringBase64('YQ==');

        expect($simple->value())
            ->toBe('SGVsbG8gV29ybGQ=')
            ->and($noPad->value())
            ->toBe('SGVsbG8h')
            ->and($twoPad->value())
            ->toBe('YQ==');
    });

    it('rejects base64 strings with invalid padding despite valid characters', function (): void {
        expect(fn() => new StringBase64('Y★Q='))
            ->toThrow(Base64StringTypeException::class);
    });

    it('rejects malformed base64 that non-strict decoding would normalize', function (): void {
        expect(fn() => new StringBase64('YWJj='))
            ->toThrow(Base64StringTypeException::class);
        // "YWJj" is "abc" in base64. "YWJj=" has an extra padding which is invalid in strict mode but base64_decode($v, false) would handle it.
        // Also "SGVsbG8gV29ybGQ" (missing =)
        expect(fn() => new StringBase64('SGVsbG8gV29ybGQ'))
            ->toThrow(Base64StringTypeException::class);

        // This string contains a character (#) that is not in the base64 alphabet.
        // base64_decode($v, true) returns false.
        // base64_decode($v, false) ignores it and decodes what it can.
        expect(fn() => new StringBase64('SGVsbG8#'))
            ->toThrow(Base64StringTypeException::class);

        // This string contains a character ($) which is sometimes ignored by non-strict decoders.
        expect(fn() => new StringBase64('SGVsbG8$'))
            ->toThrow(Base64StringTypeException::class);

        // String with whitespace that non-strict decoding would ignore
        // base64_decode('SGVs bG8=', true) returns false.
        // base64_decode('SGVs bG8=', false) ignores space and returns 'Hello'.
        expect(fn() => new StringBase64('SGVs bG8='))
            ->toThrow(Base64StringTypeException::class);

        // String with invalid character (\n) that non-strict decoding would ignore
        // base64_decode("SGVsbG8\n", true) returns false.
        // base64_decode("SGVsbG8\n", false) ignores \n and returns 'Hello'.
        expect(fn() => new StringBase64("SGVsbG8\n"))
            ->toThrow(Base64StringTypeException::class);

        // This string contains a character (e.g. dot) that is rejected in strict mode
        // but might be handled differently in non-strict mode if it's considered whitespace/ignored.
        // Actually, base64_decode('YWI.=', true) returns false.
        // base64_decode('YWI.=', false) returns 'ab' (it stops at the dot or ignores it).
        // base64_encode('ab') is 'YWI='.
        // 'YWI=' !== 'YWI.=' which is true.
        expect(fn() => new StringBase64('YWI.='))
            ->toThrow(Base64StringTypeException::class);

        // This string is technically valid base64 but contains a character that
        // base64_decode(..., true) rejects, yet base64_decode(..., false) accepts
        // AND base64_encode() would return the SAME original string.
        // Actually, for $v to equal base64_encode(base64_decode($v, false)),
        // $v MUST be composed only of A-Z, a-z, 0-9, +, /, and = (with correct padding).
        // If it contains only these, base64_decode(..., true) should also accept it!
        // EXCEPT if the padding is present but logically incorrect for the content.
        // BUT base64_decode(..., false) also returns false for invalid padding in many cases.
        // Let's try "YWI=" (which is "ab").
        // "YWI=" is 4 chars, last is padding. "ab" is 16 bits.
        // 16 bits = two 6-bit chars (12 bits) + 4 bits left.
        // The 4 bits left need one more 6-bit char (to cover 4 bits) + ONE padding char.
        // So "YWI=" is actually correct padding for "ab".
        // What if we use "YWJj="?
        // "YWJj" is "abc" (24 bits = four 6-bit chars). No padding needed.
        // base64_decode("YWJj=", true) returns FALSE.
        // base64_decode("YWJj=", false) returns "abc".
        // base64_encode("abc") returns "YWJj".
        // "YWJj" !== "YWJj=" is TRUE.
        // So even here, the second condition (base64_encode !== value) catches it!

        // This string contains a null byte (\0) at the end.
        // base64_decode("SGVsbG8=\0", true) returns false (STRICT mode rejects it).
        // base64_decode("SGVsbG8=\0", false) ignores \0 and returns 'Hello'.
        // Then base64_encode('Hello') is 'SGVsbG8='.
        // 'SGVsbG8=' !== 'SGVsbG8=\0' is TRUE.
        // So the exception should be thrown in both cases?
        // Wait, if BOTH throw, then the mutant is NOT killed.
        // I need a case where ONE throws and the OTHER DOES NOT.
        expect(fn() => new StringBase64("SGVsbG8=\0"))
            ->toThrow(Base64StringTypeException::class);

        // String with two padding chars in middle (invalid)
        // base64_decode('SGV==sbG8=', true) returns false.
        // base64_decode('SGV==sbG8=', false) ignores == and returns 'HelshO'.
        // base64_encode('HelshO') is 'SGVsc2hP'.
        // 'SGVsc2hP' !== 'SGV==sbG8=' is TRUE.
        expect(fn() => new StringBase64('SGV==sbG8='))
            ->toThrow(Base64StringTypeException::class);

        // String that has spaces in it.
        // base64_decode('SGVsbG8g V29ybGQ=', true) is FALSE.
        // base64_decode('SGVsbG8g V29ybGQ=', false) is 'Hello World'.
        // base64_encode('Hello World') is 'SGVsbG8gV29ybGQ='.
        // 'SGVsbG8gV29ybGQ=' !== 'SGVsbG8g V29ybGQ=' is TRUE.
        expect(fn() => new StringBase64('SGVsbG8g V29ybGQ='))
            ->toThrow(Base64StringTypeException::class);

        // This string contains ONLY spaces.
        // base64_decode(' ', true) is FALSE.
        // base64_decode(' ', false) is EMPTY STRING.
        // base64_encode('') is EMPTY STRING.
        // '' !== ' ' is TRUE.
        expect(fn() => new StringBase64(' '))
            ->toThrow(Base64StringTypeException::class);

        // ID: 573315b4152840e5 (TrueToFalse in base64_decode)
        // We need a string that:
        // 1. base64_decode($v, true) === FALSE (strict fails)
        // 2. base64_decode($v, false) !== FALSE (non-strict passes)
        // 3. base64_encode(base64_decode($v, false)) === $v (identity holds)
        // This is only possible if $v is already in canonical form but contains characters that strict mode rejects.
        // PHP strict mode rejects ANY whitespace, but non-strict ignores it.
        // If we have a string with whitespace, base64_encode will NOT produce the whitespace.
        // So base64_encode(...) !== $v will always be true!
        // IS THERE ANY OTHER CHARACTER?
        // What about 'SGVsbG8=' and 'SGVsbG8='? (identical)
        // No.
    });

    it('throws on invalid Base64 format', function (): void {
        // Invalid characters
        expect(fn() => new StringBase64('not valid!'))
            ->toThrow(Base64StringTypeException::class, 'Expected Base64-encoded string, got "not valid!"');

        // Empty string
        expect(fn() => StringBase64::fromString(''))
            ->toThrow(Base64StringTypeException::class);

        // Invalid padding
        expect(fn() => StringBase64::fromString('YQ==='))
            ->toThrow(Base64StringTypeException::class);

        // Spaces
        expect(fn() => StringBase64::fromString('SGVs bG8='))
            ->toThrow(Base64StringTypeException::class);
    });

    it('tryFromString returns instance for valid Base64 and Undefined for invalid', function (): void {
        $ok = StringBase64::tryFromString('SGVsbG8gV29ybGQ=');
        $bad1 = StringBase64::tryFromString('not valid!');
        $bad2 = StringBase64::tryFromString('');

        expect($ok)
            ->toBeInstanceOf(StringBase64::class)
            ->and($ok->value())
            ->toBe('SGVsbG8gV29ybGQ=')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid Base64 strings and invalid mixed inputs', function (): void {
        $ok = StringBase64::tryFromMixed('SGVsbG8gV29ybGQ=');

        $stringable = new class {
            public function __toString(): string
            {
                return 'YQ==';
            }
        };
        $fromStringable = StringBase64::tryFromMixed($stringable);

        $badFormat = StringBase64::tryFromMixed('not valid!');
        $fromArray = StringBase64::tryFromMixed(['SGVsbG8=']);
        $fromNull = StringBase64::tryFromMixed(null);
        $fromScalar = StringBase64::tryFromMixed(123);
        $fromObject = StringBase64::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringBase64::class)
            ->and($ok->value())->toBe('SGVsbG8gV29ybGQ=')
            ->and($fromStringable)->toBeInstanceOf(StringBase64::class)
            ->and($fromStringable->value())->toBe('YQ==')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        $u1 = StringBase64::tryFromString('not valid!');
        $u2 = StringBase64::tryFromMixed(['base64']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('validates various valid Base64 strings', function (): void {
        $padTwo = StringBase64::fromString('YQ==');
        $padOne = StringBase64::fromString('YWI=');
        $noPad = StringBase64::fromString('YWJj');

        expect($padTwo->value())->toBe('YQ==')
            ->and($padOne->value())->toBe('YWI=')
            ->and($noPad->value())->toBe('YWJj');
    });

    it('rejects strings with invalid Base64 characters', function (string $invalidBase64): void {
        expect(fn() => StringBase64::fromString($invalidBase64))
            ->toThrow(Base64StringTypeException::class);
    })->with([
        'SGVsbG8@',   // @ is not base64
        'SGVsbG8!',   // ! is not base64
        'SGVs bG8=',  // space
        '====',       // only padding
        'SGVsbG8gV29ybGQ#', // Invalid char that non-strict decoding might ignore
    ]);

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect($v->isTypeOf(StringBase64::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringBase64', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect($v->jsonSerialize())->toBe('SGVsbG8gV29ybGQ=');
    });

    it('toString returns the Base64-encoded string', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect($v->toString())->toBe('SGVsbG8gV29ybGQ=');
    });

    it('__toString returns the value', function (): void {
        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect((string) $v)->toBe('SGVsbG8gV29ybGQ=');
    });

    it('tryFromMixed handles bool and float inputs', function (): void {
        $fromTrue = StringBase64::tryFromMixed(true);
        $fromFalse = StringBase64::tryFromMixed(false);
        $fromFloat = StringBase64::tryFromMixed(1.5);

        expect($fromTrue)->toBeInstanceOf(StringBase64::class)
            ->and($fromTrue->value())->toBe('true')
            ->and($fromFalse)->toBeInstanceOf(Undefined::class)
            ->and($fromFloat)->toBeInstanceOf(Undefined::class);
    });

    it('covers conversions for StringBase64', function (): void {
        $fromBoolTrue = StringBase64::fromBool(true);

        expect($fromBoolTrue)->toBeInstanceOf(StringBase64::class)
            ->and($fromBoolTrue->value())->toBe('true')
            ->and(fn() => StringBase64::fromBool(false))->toThrow(Base64StringTypeException::class)
            ->and(fn() => StringBase64::fromFloat(1.2))->toThrow(Base64StringTypeException::class)
            ->and(fn() => StringBase64::fromInt(123))->toThrow(Base64StringTypeException::class)
            ->and(fn() => StringBase64::fromDecimal('1.0'))->toThrow(Base64StringTypeException::class);

        $v = StringBase64::fromString('SGVsbG8gV29ybGQ=');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal for StringBase64', function (): void {
        expect(StringBase64::tryFromBool(true))->toBeInstanceOf(StringBase64::class)
            ->and(StringBase64::tryFromBool(false))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringBase64Test extends StringBase64
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
    it('StringBase64::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringBase64Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64Test::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64Test::tryFromMixed('SGVsbG8gV29ybGQ='))->toBeInstanceOf(Undefined::class)
            ->and(StringBase64Test::tryFromString('SGVsbG8gV29ybGQ='))->toBeInstanceOf(Undefined::class);
    });
});
