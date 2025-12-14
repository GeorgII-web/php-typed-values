<?php

declare(strict_types=1);

use PhpTypedValues\Exception\UuidStringTypeException;
use PhpTypedValues\String\Uuid\StringUuidV4;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts a valid lowercase UUID v4 and preserves value', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $s = new StringUuidV4($uuid);

    expect($s->value())->toBe($uuid)
        ->and($s->toString())->toBe($uuid);
});

it('normalizes uppercase input to lowercase while preserving the UUID semantics', function (): void {
    $upper = '550E8400-E29B-41D4-A716-446655440000';
    $s = StringUuidV4::fromString($upper);

    expect($s->value())->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($s->toString())->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws on empty string', function (): void {
    expect(fn() => new StringUuidV4(''))
        ->toThrow(UuidStringTypeException::class, 'Expected non-empty UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got ""');
});

it('throws when UUID version is not 4 (e.g., version 1)', function (): void {
    $v1 = '550e8400-e29b-11d4-a716-446655440000';
    expect(fn() => StringUuidV4::fromString($v1))
        ->toThrow(UuidStringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $v1 . '"');
});

it('throws when UUID variant nibble is invalid (must be 8,9,a,b)', function (): void {
    $badVariant = '550e8400-e29b-41d4-7716-446655440000';
    expect(fn() => new StringUuidV4($badVariant))
        ->toThrow(UuidStringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badVariant . '"');
});

it('throws on invalid characters or format (non-hex character)', function (): void {
    $badChar = '550e8400-e29b-41d4-a716-44665544000g';
    expect(fn() => StringUuidV4::fromString($badChar))
        ->toThrow(UuidStringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badChar . '"');
});

it('StringUuidV4::tryFromString returns value for valid UUID v4 (case-insensitive) and normalizes to lowercase', function (): void {
    $input = '550E8400-E29B-41D4-A716-446655440000';
    $v = StringUuidV4::tryFromString($input);

    expect($v)
        ->toBeInstanceOf(StringUuidV4::class)
        ->and($v->value())
        ->toBe(strtolower($input));
});

it('StringUuidV4::tryFromString returns Undefined for non-v4 or invalid UUID', function (): void {
    $u1 = StringUuidV4::tryFromString('550e8400-e29b-11d4-a716-446655440000');
    $u2 = StringUuidV4::tryFromString('not-a-uuid');

    expect($u1)->toBeInstanceOf(Undefined::class)
        ->and($u2)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    $t = new StringUuidV4('550e8400-e29b-41d4-a716-446655440000');
    expect($t->jsonSerialize())->toBeString();
});

it('__toString returns normalized lowercase UUID v4', function (): void {
    $input = '550E8400-E29B-41D4-A716-446655440000';
    $u = StringUuidV4::fromString($input);

    expect((string) $u)->toBe(strtolower($input))
        ->and($u->__toString())->toBe(strtolower($input));
});

it('tryFromMixed returns instance for valid UUID v4 strings', function (): void {
    $fromString = StringUuidV4::tryFromMixed('550e8400-e29b-41d4-a716-446655440000');
    $fromStringable = StringUuidV4::tryFromMixed(new class {
        public function __toString(): string
        {
            return '550E8400-E29B-41D4-A716-446655440000';
        }
    });

    expect($fromString)
        ->toBeInstanceOf(StringUuidV4::class)
        ->and($fromString->value())
        ->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($fromStringable)
        ->toBeInstanceOf(StringUuidV4::class)
        ->and($fromStringable->value())
        ->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('tryFromMixed returns Undefined for invalid or non-convertible values', function (): void {
    $fromInvalidUuid = StringUuidV4::tryFromMixed('not-a-uuid');
    $fromArray = StringUuidV4::tryFromMixed([]);
    $fromObject = StringUuidV4::tryFromMixed(new stdClass());
    $fromInt = StringUuidV4::tryFromMixed(123);

    expect($fromInvalidUuid)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromArray)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromObject)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromInt)
        ->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringUuidV4', function (): void {
    $u = new StringUuidV4('550e8400-e29b-41d4-a716-446655440000');
    expect($u->isEmpty())->toBeFalse();
});
