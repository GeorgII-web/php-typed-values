<?php

declare(strict_types=1);

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringNonEmpty::tryFromString returns value for non-empty string', function (): void {
    $v = StringNonEmpty::tryFromString('abc');

    expect($v)
        ->toBeInstanceOf(StringNonEmpty::class)
        ->and($v->value())
        ->toBe('abc');
});

it('StringNonEmpty::tryFromString returns Undefined for empty string', function (): void {
    $u = StringNonEmpty::tryFromString('');

    expect($u)->toBeInstanceOf(Undefined::class);
});

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

it('jsonSerialize returns string', function (): void {
    expect(StringNonEmpty::tryFromString('hello')->jsonSerialize())->toBeString();
});

it('__toString returns the original non-empty string', function (): void {
    $s = new StringNonEmpty('world');
    expect((string) $s)->toBe('world')
        ->and($s->__toString())->toBe('world');
});

it('isEmpty is always false for StringNonEmpty', function (): void {
    $s = new StringNonEmpty('x');
    expect($s->isEmpty())->toBeFalse();
});
