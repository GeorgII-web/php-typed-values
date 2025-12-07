<?php

declare(strict_types=1);

use PhpTypedValues\String\StringUuidV7;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringUuidV7::tryFromString returns value for valid UUID v7 (case-insensitive) and normalizes to lowercase', function (): void {
    $input = '01890F2A-5BCD-7DEF-8ABC-1234567890AB'; // uppercase
    $v = StringUuidV7::tryFromString($input);

    expect($v)
        ->toBeInstanceOf(StringUuidV7::class)
        ->and($v->value())
        ->toBe(strtolower($input));
});

it('StringUuidV7::tryFromString returns Undefined for non-v7 or invalid UUID', function (): void {
    // v4-like example (13th char = 4), should fail for v7 type
    $u1 = StringUuidV7::tryFromString('550e8400-e29b-41d4-a716-446655440000');
    $u2 = StringUuidV7::tryFromString('not-a-uuid');

    expect($u1)->toBeInstanceOf(Undefined::class)
        ->and($u2)->toBeInstanceOf(Undefined::class);
});
