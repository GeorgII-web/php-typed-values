<?php

declare(strict_types=1);

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\String\StringNonBlank;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringNonBlank accepts non-blank strings and preserves value/toString', function (): void {
    $v = new StringNonBlank(' hi ');

    expect($v->value())->toBe(' hi ')
        ->and($v->toString())->toBe(' hi ')
        ->and((string) $v)->toBe(' hi ');
});

it('StringNonBlank throws on empty or whitespace-only strings', function (): void {
    expect(fn() => new StringNonBlank(''))
        ->toThrow(StringTypeException::class, 'Expected non-blank string, got ""')
        ->and(fn() => StringNonBlank::fromString("  \t  "))
        // Do not assert exact whitespace count (tabs vs spaces may render differently across environments)
        ->toThrow(StringTypeException::class, 'Expected non-blank string, got "');
});

it('StringNonBlank::tryFromString returns value for non-blank and Undefined for blank', function (): void {
    $ok = StringNonBlank::tryFromString('x');
    $bad = StringNonBlank::tryFromString('   ');

    expect($ok)->toBeInstanceOf(StringNonBlank::class)
        ->and($ok->value())->toBe('x')
        ->and($bad)->toBeInstanceOf(Undefined::class);
});

it('StringNonBlank for an empty string', function (): void {
    expect(StringNonBlank::tryFromString(''))
        ->toBeInstanceOf(Undefined::class);

    expect(fn() => StringNonBlank::fromString(''))
        ->toThrow(StringTypeException::class, 'Expected non-blank string, got ""');
});

it('jsonSerialize returns string', function (): void {
    expect(StringNonBlank::tryFromString('hello')->jsonSerialize())->toBeString();
});
