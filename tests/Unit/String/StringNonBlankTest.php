<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\StringTypeException;
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

it('tryFromMixed handles non-blank strings, stringable, and invalid mixed inputs', function (): void {
    // valid non-blank
    $ok = StringNonBlank::tryFromMixed('hello');

    // stringable producing non-blank
    $stringable = new class {
        public function __toString(): string
        {
            return 'world';
        }
    };
    $fromStringable = StringNonBlank::tryFromMixed($stringable);

    // invalid blank
    $blank = StringNonBlank::tryFromMixed('   ');

    // invalid mixed
    $fromArray = StringNonBlank::tryFromMixed(['x']);
    $fromNull = StringNonBlank::tryFromMixed(null);
    $fromObject = StringNonBlank::tryFromMixed(new stdClass());

    expect($ok)->toBeInstanceOf(StringNonBlank::class)
        ->and($ok->value())->toBe('hello')
        ->and($fromStringable)->toBeInstanceOf(StringNonBlank::class)
        ->and($fromStringable->value())->toBe('world')
        ->and($blank)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringNonBlank', function (): void {
    $v = new StringNonBlank('x');
    expect($v->isEmpty())->toBeFalse();
});

it('isUndefined is always false for StringNonBlank', function (): void {
    $v = new StringNonBlank('x');
    expect($v->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringNonBlank::fromString('test');
    expect($v->isTypeOf(StringNonBlank::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringNonBlank::fromString('test');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringNonBlank::fromString('test');
    expect($v->isTypeOf('NonExistentClass', StringNonBlank::class, 'AnotherClass'))->toBeTrue();
});
