<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\EmailStringTypeException;
use PhpTypedValues\String\Specific\StringEmail;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts valid email, preserves value/toString', function (): void {
    $e = new StringEmail('User.Name+tag@Example.COM');

    expect($e->value())
        ->toBe('User.Name+tag@Example.COM')
        ->and($e->toString())
        ->toBe('User.Name+tag@Example.COM')
        ->and((string) $e)
        ->toBe('User.Name+tag@Example.COM');
});

it('throws EmailStringTypeException on empty or invalid emails', function (): void {
    expect(fn() => new StringEmail(''))
        ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got ""')
        ->and(fn() => StringEmail::fromString(' User.Name+tag@Example.COM '))
        ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got " User.Name+tag@Example.COM "')
        ->and(fn() => StringEmail::fromString('not-an-email'))
        ->toThrow(EmailStringTypeException::class, 'Expected valid email address, got "not-an-email"');
});

it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
    $ok = StringEmail::tryFromString('admin@Example.org');
    $bad = StringEmail::tryFromString('invalid');

    expect($ok)
        ->toBeInstanceOf(StringEmail::class)
        ->and($ok->value())
        ->toBe('admin@Example.org')
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
