<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\String\StringUuidV4;

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
        ->toThrow(StringTypeException::class, 'Expected non-empty UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got ""');
});

it('throws when UUID version is not 4 (e.g., version 1)', function (): void {
    $v1 = '550e8400-e29b-11d4-a716-446655440000';
    expect(fn() => StringUuidV4::fromString($v1))
        ->toThrow(StringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $v1 . '"');
});

it('throws when UUID variant nibble is invalid (must be 8,9,a,b)', function (): void {
    $badVariant = '550e8400-e29b-41d4-7716-446655440000';
    expect(fn() => new StringUuidV4($badVariant))
        ->toThrow(StringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badVariant . '"');
});

it('throws on invalid characters or format (non-hex character)', function (): void {
    $badChar = '550e8400-e29b-41d4-a716-44665544000g';
    expect(fn() => StringUuidV4::fromString($badChar))
        ->toThrow(StringTypeException::class, 'Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badChar . '"');
});

it('generate produces a valid lowercase UUID v4 and different values across calls', function (): void {
    $a = StringUuidV4::generate();
    $b = StringUuidV4::generate();

    $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    expect($a->toString())->toMatch($regex)
        ->and($a->value())->toBe($a->toString())
        ->and($a->toString())->toBe(strtolower($a->toString()))
        // Two subsequently generated UUIDs should not be equal with overwhelmingly high probability
        ->and($a->toString())->not->toBe($b->toString())
        ->and($b->toString())->toMatch($regex)
        ->and($b->toString())->toBe(strtolower($b->toString()));
});
