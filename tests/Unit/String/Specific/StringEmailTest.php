<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\EmailStringTypeException;
use PhpTypedValues\String\Specific\StringEmail;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringEmail', function () {
    it('accepts valid email, preserves value/toString', function (): void {
        $e = new StringEmail('User.Name+tag@Composite.COM');

        expect($e->value())
            ->toBe('User.Name+tag@Composite.COM')
            ->and($e->toString())
            ->toBe('User.Name+tag@Composite.COM')
            ->and((string) $e)
            ->toBe('User.Name+tag@Composite.COM');
    });

    it('throws EmailStringTypeException on empty or invalid emails', function (): void {
        expect(fn() => new StringEmail(''))
            ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got ""')
            ->and(fn() => StringEmail::fromString(' User.Name+tag@Composite.COM '))
            ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got " User.Name+tag@Composite.COM "')
            ->and(fn() => StringEmail::fromString('not-an-email'))
            ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got "not-an-email"');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringEmail::tryFromString('admin@Composite.org');
        $bad = StringEmail::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringEmail::class)
            ->and($ok->value())
            ->toBe('admin@Composite.org')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringEmail::tryFromString('hello@domain.com')->jsonSerialize())->toBeString();
    });

    it('tryFromMixed handles valid/invalid emails, stringable, and invalid mixed inputs', function (): void {
        // valid email as string
        $ok = StringEmail::tryFromMixed('admin@example.org');

        // stringable producing a valid email
        $stringable = new class {
            public function __toString(): string
            {
                return 'user@domain.com';
            }
        };
        $fromStringable = StringEmail::tryFromMixed($stringable);

        // invalid inputs
        $bad = StringEmail::tryFromMixed('not-an-email');
        $fromArray = StringEmail::tryFromMixed(['x']);
        $fromNull = StringEmail::tryFromMixed(null);
        $fromScalar = StringEmail::tryFromMixed(123);
        $fromObject = StringEmail::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringEmail::class)
            ->and($ok->value())->toBe('admin@example.org')
            ->and($fromStringable)->toBeInstanceOf(StringEmail::class)
            ->and($fromStringable->value())->toBe('user@domain.com')
            ->and($bad)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromScalar)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringEmail', function (): void {
        $e = new StringEmail('user@example.com');
        expect($e->isEmpty())->toBeFalse();
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        // Valid instance
        $ok = StringEmail::fromString('john.doe@example.com');

        // Invalid via tryFrom*: malformed email and non-string mixed
        $u1 = StringEmail::tryFromString('not-an-email');
        $u2 = StringEmail::tryFromMixed(['x']);

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue()
            ->and($u2->isUndefined())->toBeTrue();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringEmail::fromString('test@example.com');
        expect($v->isTypeOf(StringEmail::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringEmail::fromString('test@example.com');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringEmail::fromString('test@example.com');
        expect($v->isTypeOf('NonExistentClass', StringEmail::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringEmail', function (): void {
        // These throw because "true", "1.2", "123" are not valid emails
        expect(fn() => StringEmail::fromBool(true))->toThrow(EmailStringTypeException::class)
            ->and(fn() => StringEmail::fromFloat(1.2))->toThrow(EmailStringTypeException::class)
            ->and(fn() => StringEmail::fromInt(123))->toThrow(EmailStringTypeException::class);

        $v = StringEmail::fromString('user@example.com');
        expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return Undefined for StringEmail', function (): void {
        expect(StringEmail::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringEmail::tryFromFloat(1.2))->toBeInstanceOf(Undefined::class)
            ->and(StringEmail::tryFromInt(123))->toBeInstanceOf(Undefined::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringEmailTest extends StringEmail
{
    public static function fromBool(bool $value): static
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
    it('StringEmail::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringEmailTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringEmailTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringEmailTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringEmailTest::tryFromMixed('test@example.com'))->toBeInstanceOf(Undefined::class)
            ->and(StringEmailTest::tryFromString('test@example.com'))->toBeInstanceOf(Undefined::class);
    });
});
