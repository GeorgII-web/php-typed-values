<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\Sha512StringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringSha512;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(StringSha512::class);

describe('StringSha512', function () {
    $validHash = str_repeat('a', 128);

    it('accepts valid SHA-512 hash, preserves value/toString and casts via __toString', function () use ($validHash): void {
        $s = new StringSha512($validHash);

        expect($s->value())
            ->toBe($validHash)
            ->and($s->toString())
            ->toBe($validHash)
            ->and((string) $s)
            ->toBe($validHash);
    });

    it('throws Sha512StringTypeException on empty or invalid SHA-512 hashes', function (string $value, string $message): void {
        expect(fn() => new StringSha512($value))
            ->toThrow(Sha512StringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty SHA-512 hash'],
        ['not-a-hash', 'Expected valid SHA-512 hash, got "not-a-hash"'],
        [str_repeat('a', 127), 'Expected valid SHA-512 hash, got "' . str_repeat('a', 127) . '"'],
        [str_repeat('a', 129), 'Expected valid SHA-512 hash, got "' . str_repeat('a', 129) . '"'],
        [str_repeat('g', 128), 'Expected valid SHA-512 hash, got "' . str_repeat('g', 128) . '"'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function () use ($validHash): void {
        $ok = StringSha512::tryFromString($validHash);
        $bad = StringSha512::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringSha512::class)
            ->and($ok->value())
            ->toBe($validHash)
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function () use ($validHash): void {
        expect(StringSha512::fromString($validHash)->jsonSerialize())->toBe($validHash);
    });

    it('tryFromMixed returns instance for valid hashes and Undefined for invalid or non-convertible', function () use ($validHash): void {
        $fromString = StringSha512::tryFromMixed($validHash);
        $fromStringable = StringSha512::tryFromMixed(new class($validHash) {
            public function __construct(private string $val)
            {
            }

            public function __toString(): string
            {
                return $this->val;
            }
        });
        $fromInvalidType = StringSha512::tryFromMixed([]);
        $fromInvalidValue = StringSha512::tryFromMixed('invalid');
        $fromNull = StringSha512::tryFromMixed(null);
        $fromObject = StringSha512::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringSha512::class)
            ->and($fromString->value())
            ->toBe($validHash)
            ->and($fromStringable)
            ->toBeInstanceOf(StringSha512::class)
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

    it('isEmpty is always false for StringSha512', function () use ($validHash): void {
        $s = new StringSha512($validHash);
        expect($s->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringSha512', function () use ($validHash): void {
        $s = new StringSha512($validHash);
        expect($s->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function () use ($validHash): void {
        $v = StringSha512::fromString($validHash);
        expect($v->isTypeOf(StringSha512::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () use ($validHash): void {
        $v = StringSha512::fromString($validHash);
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () use ($validHash): void {
        $v = StringSha512::fromString($validHash);
        expect($v->isTypeOf('NonExistentClass', StringSha512::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringSha512', function () use ($validHash): void {
        expect(fn() => StringSha512::fromInt(123))->toThrow(Sha512StringTypeException::class);

        $v = StringSha512::fromString($validHash);
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringSha512::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringSha512Test extends StringSha512
{
    public static function fromBool(bool $value): static
    {
        throw new Sha512StringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new Sha512StringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Sha512StringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Sha512StringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new Sha512StringTypeException('test');
    }
}

describe('Throwing static StringSha512', function () {
    $validHash = str_repeat('a', 128);

    it('StringSha512::tryFrom* returns Undefined when exception occurs (coverage)', function () use ($validHash): void {
        expect(StringSha512Test::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512Test::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512Test::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512Test::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512Test::tryFromMixed($validHash))->toBeInstanceOf(Undefined::class)
            ->and(StringSha512Test::tryFromString($validHash))->toBeInstanceOf(Undefined::class);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringSha512::fromNull(null))
            ->toThrow(Sha512StringTypeException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringSha512::toNull())
            ->toThrow(Sha512StringTypeException::class);
    });
});
