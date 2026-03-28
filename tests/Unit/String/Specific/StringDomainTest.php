<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\DomainStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringDomain;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

describe('StringDomain', function () {
    $validDomain = 'example.com';

    it('accepts valid domain, preserves value/toString and casts via __toString', function (string $domain): void {
        $s = new StringDomain($domain);

        expect($s->value())
            ->toBe($domain)
            ->and($s->toString())
            ->toBe($domain)
            ->and((string) $s)
            ->toBe($domain);
    })->with([
        ['example.com'],
        ['sub.example.com'],
        ['a.b.c.d.e.f.g.h.i.j'],
        ['my-domain.org'],
        ['xn--6qq79v.com'], // Punycode
        ['localhost.localdomain'],
    ]);

    it('throws DomainStringTypeException on empty or invalid domains', function (string $value, string $message): void {
        expect(fn() => new StringDomain($value))
            ->toThrow(DomainStringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty domain name'],
        ['not-a-domain', 'Expected valid domain name, got "not-a-domain"'],
        ['.example.com', 'Expected valid domain name, got ".example.com"'],
        ['example.', 'Expected valid domain name, got "example."'],
        ['example..com', 'Expected valid domain name, got "example..com"'],
        ['-example.com', 'Expected valid domain name, got "-example.com"'],
        ['example-.com', 'Expected valid domain name, got "example-.com"'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function () use ($validDomain): void {
        $ok = StringDomain::tryFromString($validDomain);
        $bad = StringDomain::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringDomain::class)
            ->and($ok->value())
            ->toBe($validDomain)
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function () use ($validDomain): void {
        expect(StringDomain::fromString($validDomain)->jsonSerialize())->toBe($validDomain);
    });

    it('tryFromMixed returns instance for valid domains and Undefined for invalid or non-convertible', function () use ($validDomain): void {
        $fromString = StringDomain::tryFromMixed($validDomain);
        $fromStringable = StringDomain::tryFromMixed(new class($validDomain) {
            public function __construct(private string $val)
            {
            }

            public function __toString(): string
            {
                return $this->val;
            }
        });
        $fromInvalidType = StringDomain::tryFromMixed([]);
        $fromInvalidValue = StringDomain::tryFromMixed('invalid');
        $fromNull = StringDomain::tryFromMixed(null);
        $fromObject = StringDomain::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringDomain::class)
            ->and($fromString->value())
            ->toBe($validDomain)
            ->and($fromStringable)
            ->toBeInstanceOf(StringDomain::class)
            ->and($fromStringable->value())
            ->toBe($validDomain)
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringDomain', function () use ($validDomain): void {
        $s = new StringDomain($validDomain);
        expect($s->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringDomain', function () use ($validDomain): void {
        $s = new StringDomain($validDomain);
        expect($s->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function () use ($validDomain): void {
        $v = StringDomain::fromString($validDomain);
        expect($v->isTypeOf(StringDomain::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () use ($validDomain): void {
        $v = StringDomain::fromString($validDomain);
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () use ($validDomain): void {
        $v = StringDomain::fromString($validDomain);
        expect($v->isTypeOf('NonExistentClass', StringDomain::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringDomain', function () use ($validDomain): void {
        expect(fn() => StringDomain::fromInt(123))->toThrow(DomainStringTypeException::class);

        $v = StringDomain::fromString($validDomain);
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringDomain::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringDomain::tryFromFloat(-1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringDomain::tryFromInt(-123))->toBeInstanceOf(Undefined::class)
            ->and(StringDomain::tryFromDecimal('-1.1'))->toBeInstanceOf(Undefined::class);
    });

    it('fromDecimal and fromFloat throw exception for non-domain values', function (): void {
        expect(fn() => StringDomain::fromDecimal('-1.1'))->toThrow(DomainStringTypeException::class)
            ->and(fn() => StringDomain::fromFloat(-1.1))->toThrow(DomainStringTypeException::class);
    });

    it('fromBool and fromInt throw exception for non-domain values', function (): void {
        expect(fn() => StringDomain::fromBool(true))->toThrow(DomainStringTypeException::class)
            ->and(fn() => StringDomain::fromInt(-123))->toThrow(DomainStringTypeException::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringDomainTest extends StringDomain
{
    public static function fromBool(bool $value): static
    {
        throw new DomainStringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new DomainStringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new DomainStringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new DomainStringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new DomainStringTypeException('test');
    }
}

describe('Throwing static StringDomain', function () {
    $validDomain = 'example.com';

    it('StringDomain::tryFrom* returns Undefined when exception occurs (coverage)', function () use ($validDomain): void {
        expect(StringDomainTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringDomainTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringDomainTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringDomainTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringDomainTest::tryFromMixed($validDomain))->toBeInstanceOf(Undefined::class)
            ->and(StringDomainTest::tryFromString($validDomain))->toBeInstanceOf(Undefined::class);
    });
});
