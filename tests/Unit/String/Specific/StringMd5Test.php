<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\Md5StringTypeException;
use PhpTypedValues\String\Specific\StringMd5;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringMd5', function () {
    it('accepts valid MD5 hash and preserves case', function (): void {
        $lowercase = new StringMd5('5d41402abc4b2a76b9719d911017c592');
        $uppercase = new StringMd5('5D41402ABC4B2A76B9719D911017C592');
        $mixed = new StringMd5('5d41402AbC4b2A76B9719d911017c592');

        expect($lowercase->value())
            ->toBe('5d41402abc4b2a76b9719d911017c592')
            ->and($uppercase->value())
            ->toBe('5D41402ABC4B2A76B9719D911017C592')
            ->and($mixed->value())
            ->toBe('5d41402AbC4b2A76B9719d911017c592');
    });

    it('throws on invalid MD5 format', function (): void {
        // Too short
        expect(fn() => new StringMd5('5d41402abc4b2a76b9719d911017c59'))
            ->toThrow(Md5StringTypeException::class, 'Expected MD5 hash (32 hex characters), got "5d41402abc4b2a76b9719d911017c59"');

        // Too long
        expect(fn() => StringMd5::fromString('5d41402abc4b2a76b9719d911017c5922'))
            ->toThrow(Md5StringTypeException::class, 'Expected MD5 hash (32 hex characters), got "5d41402abc4b2a76b9719d911017c5922"');

        // Invalid characters
        expect(fn() => StringMd5::fromString('5d41402abc4b2a76b9719d911017c59z'))
            ->toThrow(Md5StringTypeException::class, 'Expected MD5 hash (32 hex characters), got "5d41402abc4b2a76b9719d911017c59z"');

        // Empty string
        expect(fn() => StringMd5::fromString(''))
            ->toThrow(Md5StringTypeException::class);
    });

    it('tryFromString returns instance for valid hash and Undefined for invalid', function (): void {
        $ok = StringMd5::tryFromString('5d41402abc4b2a76b9719d911017c592');
        $bad1 = StringMd5::tryFromString('invalid');
        $bad2 = StringMd5::tryFromString('5d41402abc4b2a76b9719d911017c59'); // too short

        expect($ok)
            ->toBeInstanceOf(StringMd5::class)
            ->and($ok->value())
            ->toBe('5d41402abc4b2a76b9719d911017c592')
            ->and($bad1)
            ->toBeInstanceOf(Undefined::class)
            ->and($bad2)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid MD5 hashes and invalid mixed inputs', function (): void {
        // valid hash as string (lowercase)
        $ok = StringMd5::tryFromMixed('5d41402abc4b2a76b9719d911017c592');

        // valid hash as string (uppercase)
        $okUpper = StringMd5::tryFromMixed('5D41402ABC4B2A76B9719D911017C592');

        // stringable producing a valid hash
        $stringable = new class {
            public function __toString(): string
            {
                return '098f6bcd4621d373cade4e832627b4f6';
            }
        };
        $fromStringable = StringMd5::tryFromMixed($stringable);

        // invalid inputs
        $badFormat = StringMd5::tryFromMixed('invalid');
        $fromArray = StringMd5::tryFromMixed(['5d41402abc4b2a76b9719d911017c592']);
        $fromNull = StringMd5::tryFromMixed(null);
        $fromScalar = StringMd5::tryFromMixed(123); // not a valid hash
        $fromObject = StringMd5::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringMd5::class)
            ->and($ok->value())->toBe('5d41402abc4b2a76b9719d911017c592')
            ->and($okUpper)->toBeInstanceOf(StringMd5::class)
            ->and($okUpper->value())->toBe('5D41402ABC4B2A76B9719D911017C592')
            ->and($fromStringable)->toBeInstanceOf(StringMd5::class)
            ->and($fromStringable->value())->toBe('098f6bcd4621d373cade4e832627b4f6')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance
        $ok = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');

        // Invalid via tryFrom*
        $u1 = StringMd5::tryFromString('invalid');
        $u2 = StringMd5::tryFromMixed(['hash']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('handles uppercase input and preserves case', function (): void {
        $uppercase = 'D41D8CD98F00B204E9800998ECF8427E';
        $hash = StringMd5::fromString($uppercase);

        expect($hash->value())->toBe('D41D8CD98F00B204E9800998ECF8427E')
            ->and($hash->toString())->toBe('D41D8CD98F00B204E9800998ECF8427E');
    });

    it('validates all hexadecimal characters are accepted', function (): void {
        // All hex digits present (lowercase)
        $allHexLower = 'abcdef0123456789abcdef0123456789';
        $hashLower = StringMd5::fromString($allHexLower);

        // All hex digits present (uppercase)
        $allHexUpper = 'ABCDEF0123456789ABCDEF0123456789';
        $hashUpper = StringMd5::fromString($allHexUpper);

        expect($hashLower->value())->toBe($allHexLower)
            ->and($hashUpper->value())->toBe($allHexUpper);
    });

    it('rejects hashes with invalid hex characters', function (string $invalidHash): void {
        expect(fn() => StringMd5::fromString($invalidHash))
            ->toThrow(Md5StringTypeException::class);
    })->with([
        '5d41402abc4b2a76b9719d911017c59g', // 'g' is not hex
        '5d41402abc4b2a76b9719d911017c59!', // special char
        '5d41402abc4b2a76b9719d911017c5 2', // space
        'zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', // all invalid
    ]);

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
        expect($v->isTypeOf(StringMd5::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isEmpty is always false for StringMd5', function (): void {
        $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
        expect($v->jsonSerialize())->toBe('5d41402abc4b2a76b9719d911017c592');
    });

    it('covers conversions for StringMd5', function (): void {
        // fromBool, fromFloat, fromInt throw for StringMd5 because values like 'true', '1.0' are not MD5
        expect(fn() => StringMd5::fromBool(true))->toThrow(Md5StringTypeException::class)
            ->and(fn() => StringMd5::fromFloat(1.2))->toThrow(Md5StringTypeException::class)
            ->and(fn() => StringMd5::fromInt(123))->toThrow(Md5StringTypeException::class);

        $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
        expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\Integer\IntegerTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return Undefined for StringMd5', function (): void {
        expect(StringMd5::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5::tryFromInt(123))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringMd5Test extends StringMd5
{
    public function __construct(string $value)
    {
        throw new Exception('test');
    }
}

describe('Throwing static', function () {
    it('StringMd5::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringMd5Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5Test::tryFromMixed('5d41402abc4b2a76b9719d911017c592'))->toBeInstanceOf(Undefined::class)
            ->and(StringMd5Test::tryFromString('5d41402abc4b2a76b9719d911017c592'))->toBeInstanceOf(Undefined::class);
    });
});
