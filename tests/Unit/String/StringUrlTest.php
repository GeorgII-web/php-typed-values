<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\UrlStringTypeException;
use PhpTypedValues\Integer\String\Specific\StringUrl;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts valid URL, preserves value/toString and casts via __toString', function (): void {
    $u = new StringUrl('https://example.com/path?x=1#anchor');

    expect($u->value())
        ->toBe('https://example.com/path?x=1#anchor')
        ->and($u->toString())
        ->toBe('https://example.com/path?x=1#anchor')
        ->and((string) $u)
        ->toBe('https://example.com/path?x=1#anchor');
});

it('throws UrlStringTypeException on empty or invalid URLs', function (): void {
    expect(fn() => new StringUrl(''))
        ->toThrow(UrlStringTypeException::class, 'Expected valid URL, got ""')
        ->and(fn() => StringUrl::fromString('not-a-url'))
        ->toThrow(UrlStringTypeException::class, 'Expected valid URL, got "not-a-url"');
});

it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
    $ok = StringUrl::tryFromString('https://www.example.org');
    $bad = StringUrl::tryFromString('notaurl');

    expect($ok)
        ->toBeInstanceOf(StringUrl::class)
        ->and($ok->value())
        ->toBe('https://www.example.org')
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(StringUrl::tryFromString('https://www.example.org')->jsonSerialize())->toBeString();
});

it('tryFromMixed returns instance for valid URL strings', function (): void {
    $fromString = StringUrl::tryFromMixed('https://example.com');
    $fromStringable = StringUrl::tryFromMixed(new class {
        public function __toString(): string
        {
            return 'http://test.org/path';
        }
    });

    expect($fromString)
        ->toBeInstanceOf(StringUrl::class)
        ->and($fromString->value())
        ->toBe('https://example.com')
        ->and($fromStringable)
        ->toBeInstanceOf(StringUrl::class)
        ->and($fromStringable->value())
        ->toBe('http://test.org/path');
});

it('tryFromMixed returns Undefined for invalid or non-convertible values', function (): void {
    $fromInvalidUrl = StringUrl::tryFromMixed('not-a-url');
    $fromArray = StringUrl::tryFromMixed([]);
    $fromObject = StringUrl::tryFromMixed(new stdClass());
    $fromNull = StringUrl::tryFromMixed(null);

    expect($fromInvalidUrl)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromArray)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromObject)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromNull)
        ->toBeInstanceOf(Undefined::class);
});

it('fromString creates instance with correct value', function (): void {
    $url = StringUrl::fromString('https://example.com/path');

    expect($url)
        ->toBeInstanceOf(StringUrl::class)
        ->and($url->value())
        ->toBe('https://example.com/path');
});

it('isEmpty is always false for StringUrl', function (): void {
    $u = new StringUrl('https://example.com');
    expect($u->isEmpty())->toBeFalse();
});

it('isUndefined is always false for StringUrl', function (): void {
    $u = new StringUrl('https://example.com');
    expect($u->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringUrl::fromString('https://example.com');
    expect($v->isTypeOf(StringUrl::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringUrl::fromString('https://example.com');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringUrl::fromString('https://example.com');
    expect($v->isTypeOf('NonExistentClass', StringUrl::class, 'AnotherClass'))->toBeTrue();
});
