<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\Md5StringTypeException;
use PhpTypedValues\Integer\String\Specific\StringMd5;
use PhpTypedValues\Undefined\Alias\Undefined;

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

it('fromHash creates MD5 from input string', function (): void {
    $hash = StringMd5::hash('hello');

    expect($hash->value())->toBe('5d41402abc4b2a76b9719d911017c592')
        ->and($hash->toString())->toBe('5d41402abc4b2a76b9719d911017c592');
});

it('fromHash with various inputs produces correct hashes', function (string $input, string $expectedHash): void {
    $hash = StringMd5::hash($input);
    expect($hash->value())->toBe($expectedHash);
})->with([
    ['hello', '5d41402abc4b2a76b9719d911017c592'],
    ['world', '7d793037a0760186574b0282f2f435e7'],
    ['', 'd41d8cd98f00b204e9800998ecf8427e'], // MD5 of empty string
    ['The quick brown fox jumps over the lazy dog', '9e107d9d372bb6826bd81d3542a419d6'],
    ['123', '202cb962ac59075b964b07152d234b70'],
]);

it('fromString and fromHash produce same result', function (): void {
    $input = 'test';
    $expectedHash = md5($input);

    $fromString = StringMd5::fromString($expectedHash);
    $fromHash = StringMd5::hash($input);

    expect($fromString->value())->toBe($fromHash->value())
        ->and($fromString->value())->toBe($expectedHash);
});

it('toString returns the MD5 hash', function (): void {
    $hash = StringMd5::hash('example');

    expect($hash->toString())->toBe($hash->value())
        ->and($hash->toString())->toBe('1a79a4d60de6718e8e5b326e338ae533');
});

it('__toString magic method works correctly', function (): void {
    $hash = StringMd5::hash('test');

    expect((string) $hash)->toBe('098f6bcd4621d373cade4e832627b4f6')
        ->and($hash . ' suffix')->toBe('098f6bcd4621d373cade4e832627b4f6 suffix');
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

it('jsonSerialize returns the hash string', function (): void {
    $hash = StringMd5::hash('json');

    expect($hash->jsonSerialize())->toBeString()
        ->and($hash->jsonSerialize())->toBe($hash->value())
        ->and(json_encode($hash))->toBe('"' . $hash->value() . '"');
});

it('isEmpty is always false for StringMd5', function (): void {
    $hash = StringMd5::hash('test');
    expect($hash->isEmpty())->toBeFalse();
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

it('round-trip conversion preserves hash: string → hash → string', function (): void {
    $input = 'round-trip-test';
    $hash = StringMd5::hash($input);
    $hashString = $hash->toString();
    $reconstructed = StringMd5::fromString($hashString);

    expect($reconstructed->value())->toBe($hash->value())
        ->and($reconstructed->value())->toBe(md5($input));
});

it('value and toString return the same string', function (): void {
    $hash = StringMd5::hash('consistency');

    expect($hash->value())->toBe($hash->toString());
});

it('handles uppercase input and preserves case', function (): void {
    $uppercase = 'D41D8CD98F00B204E9800998ECF8427E';
    $hash = StringMd5::fromString($uppercase);

    expect($hash->value())->toBe('D41D8CD98F00B204E9800998ECF8427E')
        ->and($hash->toString())->toBe('D41D8CD98F00B204E9800998ECF8427E');
});

it('multiple fromHash calls with same input produce identical results', function (): void {
    $input = 'duplicate';
    $hash1 = StringMd5::hash($input);
    $hash2 = StringMd5::hash($input);

    expect($hash1->value())->toBe($hash2->value());
});

it('fromHash handles special characters and unicode', function (): void {
    $special = 'Hello, 世界! @#$%^&*()';
    $hash = StringMd5::hash($special);

    expect($hash->value())->toMatch('/^[a-f0-9]{32}$/')
        ->and($hash->value())->toBe(md5($special));
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

it('round-trip: hash input → get string → hash again produces same result', function (): void {
    $input = 'cycle-test';
    $hash1 = StringMd5::hash($input);
    $retrieved = $hash1->value();
    $hash2 = StringMd5::fromString($retrieved);

    expect($hash1->value())->toBe($hash2->value())
        ->and($hash2->value())->toBe(md5($input));
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
    expect($v->isTypeOf(StringMd5::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringMd5::fromString('5d41402abc4b2a76b9719d911017c592');
    expect($v->isTypeOf('NonExistentClass', StringMd5::class, 'AnotherClass'))->toBeTrue();
});
