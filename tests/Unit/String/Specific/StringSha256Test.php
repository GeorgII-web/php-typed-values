<?php

declare(strict_types=1);

namespace Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\Sha256StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringSha256;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringSha256', function () {
    $validHash = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    it('accepts valid SHA-256 hash, preserves value/toString and casts via __toString', function () use ($validHash): void {
        $s = new StringSha256($validHash);

        expect($s->value())
            ->toBe($validHash)
            ->and($s->toString())
            ->toBe($validHash)
            ->and((string) $s)
            ->toBe($validHash);
    });

    it('throws Sha256StringTypeException on empty or invalid SHA-256 hashes', function (string $value, string $message): void {
        expect(fn() => new StringSha256($value))
            ->toThrow(Sha256StringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty SHA-256 hash'],
        ['not-a-hash', 'Expected valid SHA-256 hash, got "not-a-hash"'],
        [str_repeat('a', 63), 'Expected valid SHA-256 hash, got "' . str_repeat('a', 63) . '"'],
        [str_repeat('a', 65), 'Expected valid SHA-256 hash, got "' . str_repeat('a', 65) . '"'],
        [str_repeat('g', 64), 'Expected valid SHA-256 hash, got "' . str_repeat('g', 64) . '"'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function () use ($validHash): void {
        $ok = StringSha256::tryFromString($validHash);
        $bad = StringSha256::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringSha256::class)
            ->and($ok->value())
            ->toBe($validHash)
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function () use ($validHash): void {
        expect(StringSha256::fromString($validHash)->jsonSerialize())->toBe($validHash);
    });

    it('tryFromMixed returns instance for valid hashes and Undefined for invalid or non-convertible', function () use ($validHash): void {
        $fromString = StringSha256::tryFromMixed($validHash);
        $fromStringable = StringSha256::tryFromMixed(new class($validHash) {
            public function __construct(private string $val)
            {
            }

            public function __toString(): string
            {
                return $this->val;
            }
        });
        $fromInvalidType = StringSha256::tryFromMixed([]);
        $fromInvalidValue = StringSha256::tryFromMixed('invalid');
        $fromNull = StringSha256::tryFromMixed(null);
        $fromObject = StringSha256::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringSha256::class)
            ->and($fromString->value())
            ->toBe($validHash)
            ->and($fromStringable)
            ->toBeInstanceOf(StringSha256::class)
            ->and($fromStringable->value())
            ->toBe($validHash)
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringSha256', function () use ($validHash): void {
        $s = new StringSha256($validHash);
        expect($s->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringSha256', function () use ($validHash): void {
        $s = new StringSha256($validHash);
        expect($s->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function () use ($validHash): void {
        $v = StringSha256::fromString($validHash);
        expect($v->isTypeOf(StringSha256::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () use ($validHash): void {
        $v = StringSha256::fromString($validHash);
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () use ($validHash): void {
        $v = StringSha256::fromString($validHash);
        expect($v->isTypeOf('NonExistentClass', StringSha256::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringSha256', function () use ($validHash): void {
        expect(fn() => StringSha256::fromInt(123))->toThrow(Sha256StringTypeException::class);

        $v = StringSha256::fromString($validHash);
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringSha256::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringSha256Test extends StringSha256
{
    public static function fromBool(bool $value): static
    {
        throw new Sha256StringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new Sha256StringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Sha256StringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Sha256StringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new Sha256StringTypeException('test');
    }
}

describe('Throwing static StringSha256', function () {
    $validHash = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    it('StringSha256::tryFrom* returns Undefined when exception occurs (coverage)', function () use ($validHash): void {
        expect(StringSha256Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256Test::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256Test::tryFromMixed($validHash))->toBeInstanceOf(Undefined::class)
            ->and(StringSha256Test::tryFromString($validHash))->toBeInstanceOf(Undefined::class);
    });
});
