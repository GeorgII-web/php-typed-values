<?php

declare(strict_types=1);

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\String\StringUuidV7;

it('accepts a valid lowercase UUID v7 and preserves value', function (): void {
    // Matches: xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx
    $uuid = '01890f2a-5bcd-7def-8abc-1234567890ab';
    $s = new StringUuidV7($uuid);

    expect($s->value())->toBe($uuid)
        ->and($s->toString())->toBe($uuid);
});

it('normalizes uppercase input to lowercase while preserving the UUID semantics', function (): void {
    $upper = '01890F2A-5BCD-7DEF-9ABC-1234567890AB';
    $s = StringUuidV7::fromString($upper);

    expect($s->value())->toBe('01890f2a-5bcd-7def-9abc-1234567890ab')
        ->and($s->toString())->toBe('01890f2a-5bcd-7def-9abc-1234567890ab');
});

it('throws on empty string', function (): void {
    expect(fn() => new StringUuidV7(''))
        ->toThrow(StringTypeException::class, 'Expected non-empty UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got ""');
});

it('throws when UUID version is not 7 (e.g., version 4)', function (): void {
    $v4 = '550e8400-e29b-41d4-a716-446655440000';
    expect(fn() => StringUuidV7::fromString($v4))
        ->toThrow(StringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $v4 . '"');
});

it('throws when UUID variant nibble is invalid (must be 8,9,a,b)', function (): void {
    // Fourth group starts with '7' which is invalid for RFC 4122 variant
    $badVariant = '550e8400-e29b-7d14-7716-446655440000';
    expect(fn() => new StringUuidV7($badVariant))
        ->toThrow(StringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badVariant . '"');
});

it('throws on invalid characters or format (non-hex character)', function (): void {
    $badChar = '01890f2a-5bcd-7def-8abc-1234567890ag';
    expect(fn() => StringUuidV7::fromString($badChar))
        ->toThrow(StringTypeException::class, 'Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "' . $badChar . '"');
});
