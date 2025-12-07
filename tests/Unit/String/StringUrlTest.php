<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UrlStringTypeException;
use PhpTypedValues\String\StringUrl;
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
