<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\TypeException;
use PhpTypedValues\String\NonEmptyStr;

it('constructs and preserves non-empty string', function (): void {
    $s = new NonEmptyStr('hello');
    expect($s->value())->toBe('hello')
        ->and($s->toString())->toBe('hello');
});

it('allows whitespace and unicode as non-empty', function (): void {
    $w = new NonEmptyStr(' ');
    $u = NonEmptyStr::fromString('🙂');
    expect($w->value())->toBe(' ')
        ->and($u->toString())->toBe('🙂');
});

it('throws on empty string via constructor', function (): void {
    expect(fn() => new NonEmptyStr(''))
        ->toThrow(TypeException::class, 'Value must be a non-empty string');
});

it('throws on empty string via fromString', function (): void {
    expect(fn() => NonEmptyStr::fromString(''))
        ->toThrow(TypeException::class, 'Value must be a non-empty string');
});
