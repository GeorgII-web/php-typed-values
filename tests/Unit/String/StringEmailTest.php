<?php

declare(strict_types=1);

use PhpTypedValues\Exception\EmailStringTypeException;
use PhpTypedValues\String\StringEmail;
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
