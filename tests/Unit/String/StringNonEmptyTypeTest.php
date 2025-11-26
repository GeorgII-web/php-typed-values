<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\String\StringNonEmpty;

it('constructs and preserves non-empty string', function (): void {
    $s = new StringNonEmpty('hello');
    expect($s->value())->toBe('hello')
        ->and($s->toString())->toBe('hello');
});

it('allows whitespace and unicode as non-empty', function (): void {
    $w = new StringNonEmpty(' ');
    $u = StringNonEmpty::fromString('🙂');
    expect($w->value())->toBe(' ')
        ->and($u->toString())->toBe('🙂');
});

it('throws on empty string via constructor', function (): void {
    expect(fn() => new StringNonEmpty(''))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('throws on empty string via fromString', function (): void {
    expect(fn() => StringNonEmpty::fromString(''))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});
