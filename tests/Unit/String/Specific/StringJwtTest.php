<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\JwtStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringJwt;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

covers(StringJwt::class);

describe('StringJwt', function () {
    $validJwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

    it('accepts valid JWT, preserves value/toString and casts via __toString', function () use ($validJwt): void {
        $s = new StringJwt($validJwt);

        expect($s->value())
            ->toBe($validJwt)
            ->and($s->toString())
            ->toBe($validJwt)
            ->and((string) $s)
            ->toBe($validJwt);
    });

    it('throws JwtStringTypeException on empty or invalid JWTs', function (string $value, string $message): void {
        expect(fn() => new StringJwt($value))
            ->toThrow(JwtStringTypeException::class, $message);
    })->with([
        ['', 'Expected non-empty JWT'],
        ['not-a-jwt', 'Expected valid JWT, got "not-a-jwt"'],
        ['header.payload', 'Expected valid JWT, got "header.payload"'],
        ['header.payload.signature.extra', 'Expected valid JWT, got "header.payload.signature.extra"'],
        ['header..signature', 'Expected valid JWT, got "header..signature"'],
    ]);

    it('tryFromString returns instance for valid and Undefined for invalid', function () use ($validJwt): void {
        $ok = StringJwt::tryFromString($validJwt);
        $bad = StringJwt::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringJwt::class)
            ->and($ok->value())
            ->toBe($validJwt)
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function () use ($validJwt): void {
        expect(StringJwt::fromString($validJwt)->jsonSerialize())->toBe($validJwt);
    });

    it('tryFromMixed returns instance for valid JWTs and Undefined for invalid or non-convertible', function () use ($validJwt): void {
        $fromString = StringJwt::tryFromMixed($validJwt);
        $fromStringable = StringJwt::tryFromMixed(new class($validJwt) {
            public function __construct(private string $val)
            {
            }

            public function __toString(): string
            {
                return $this->val;
            }
        });
        $fromInvalidType = StringJwt::tryFromMixed([]);
        $fromInvalidValue = StringJwt::tryFromMixed('invalid');
        $fromNull = StringJwt::tryFromMixed(null);
        $fromObject = StringJwt::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringJwt::class)
            ->and($fromString->value())
            ->toBe($validJwt)
            ->and($fromStringable)
            ->toBeInstanceOf(StringJwt::class)
            ->and($fromStringable->value())
            ->toBe($validJwt)
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringJwt', function () use ($validJwt): void {
        $s = new StringJwt($validJwt);
        expect($s->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringJwt', function () use ($validJwt): void {
        $s = new StringJwt($validJwt);
        expect($s->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function () use ($validJwt): void {
        $v = StringJwt::fromString($validJwt);
        expect($v->isTypeOf(StringJwt::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function () use ($validJwt): void {
        $v = StringJwt::fromString($validJwt);
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function () use ($validJwt): void {
        $v = StringJwt::fromString($validJwt);
        expect($v->isTypeOf('NonExistentClass', StringJwt::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringJwt', function () use ($validJwt): void {
        expect(fn() => StringJwt::fromInt(123))->toThrow(JwtStringTypeException::class);

        $v = StringJwt::fromString($validJwt);
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt, tryFromDecimal return Undefined for invalid inputs', function (): void {
        expect(StringJwt::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringJwt::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringJwt::tryFromInt(123))->toBeInstanceOf(Undefined::class)
            ->and(StringJwt::tryFromDecimal('1.23'))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringJwtTest extends StringJwt
{
    public static function fromBool(bool $value): static
    {
        throw new JwtStringTypeException('test');
    }

    public static function fromDecimal(string $value): static
    {
        throw new JwtStringTypeException('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new JwtStringTypeException('test');
    }

    public static function fromInt(int $value): static
    {
        throw new JwtStringTypeException('test');
    }

    public static function fromString(string $value): static
    {
        throw new JwtStringTypeException('test');
    }
}

describe('Throwing static StringJwt', function () {
    $validJwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

    it('StringJwt::tryFrom* returns Undefined when exception occurs (coverage)', function () use ($validJwt): void {
        expect(StringJwtTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringJwtTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringJwtTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringJwtTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringJwtTest::tryFromMixed($validJwt))->toBeInstanceOf(Undefined::class)
            ->and(StringJwtTest::tryFromString($validJwt))->toBeInstanceOf(Undefined::class);
    });
});

describe('Null checks', function () {
    it('throws exception on fromNull', function () {
        expect(fn() => StringJwt::fromNull(null))
            ->toThrow(JwtStringTypeException::class);
    });

    it('throws exception on toNull', function () {
        expect(fn() => StringJwt::toNull())
            ->toThrow(JwtStringTypeException::class);
    });
});
