<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String;

use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\StringEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

covers(StringEmpty::class);

it('constructs from an empty string', function (): void {
    $c = new StringEmpty('');

    expect($c->value())->toBe('')
        ->and($c->toString())->toBe('')
        ->and((string) $c)->toBe('')
        ->and($c->isEmpty())->toBeTrue();
});

it('throws exception if string is not empty', function (): void {
    expect(fn() => new StringEmpty('hello'))
        ->toThrow(StringTypeException::class, 'Expected empty string, got "hello"');
});

it('fromString constructs from an empty string', function (): void {
    $c = StringEmpty::fromString('');
    expect($c->value())->toBe('');
});

it('tryFromString constructs from an empty string', function (): void {
    $c = StringEmpty::tryFromString('');
    expect($c)->toBeInstanceOf(StringEmpty::class)
        ->and($c->value())->toBe('');
});

it('tryFromString returns Undefined for non-empty string', function (): void {
    $c = StringEmpty::tryFromString('hello');
    expect($c)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed constructs from an empty string', function (): void {
    $c = StringEmpty::tryFromMixed('');
    expect($c)->toBeInstanceOf(StringEmpty::class)
        ->and($c->value())->toBe('');
});

it('tryFromMixed returns Undefined for non-empty string', function (): void {
    $c = StringEmpty::tryFromMixed('hello');
    expect($c)->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed returns Undefined for non-stringable mixed', function (): void {
    $c = StringEmpty::tryFromMixed([]);
    expect($c)->toBeInstanceOf(Undefined::class)
        ->and(StringEmpty::tryFromMixed(new stdClass()))->toBeInstanceOf(Undefined::class);
});

it('tryFromMixed handles various inputs for StringEmpty', function (): void {
    $fromNull = StringEmpty::tryFromMixed(null);
    $fromInt = StringEmpty::tryFromMixed(0);
    $fromEmptyStringable = StringEmpty::tryFromMixed(new class implements Stringable {
        public function __toString(): string
        {
            return '';
        }
    });
    $fromNonEmptyStringable = StringEmpty::tryFromMixed(new class implements Stringable {
        public function __toString(): string
        {
            return 'not-empty';
        }
    });

    expect($fromNull)->toBeInstanceOf(StringEmpty::class)
        ->and($fromNull->value())->toBe('')
        ->and($fromInt)->toBeInstanceOf(Undefined::class)
        ->and($fromEmptyStringable)->toBeInstanceOf(StringEmpty::class)
        ->and($fromEmptyStringable->value())->toBe('')
        ->and($fromNonEmptyStringable)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns empty string', function (): void {
    $c = new StringEmpty('');
    expect($c->jsonSerialize())->toBe('');
});

it('isUndefined returns false', function (): void {
    $c = new StringEmpty('');
    expect($c->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringEmpty::fromString('');
    expect($v->isTypeOf(StringEmpty::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringEmpty::fromString('');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringEmpty::fromString('');
    expect($v->isTypeOf('NonExistentClass', StringEmpty::class, 'AnotherClass'))->toBeTrue();
});
