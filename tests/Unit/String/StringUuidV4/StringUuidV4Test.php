<?php

declare(strict_types=1);

use PhpTypedValues\String\StringUuidV4;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringUuidV4::tryFromString returns value for valid UUID v4 (case-insensitive) and normalizes to lowercase', function (): void {
    $input = '550E8400-E29B-41D4-A716-446655440000'; // uppercase
    $v = StringUuidV4::tryFromString($input);

    expect($v)
        ->toBeInstanceOf(StringUuidV4::class)
        ->and($v->value())
        ->toBe(strtolower($input));
});

it('StringUuidV4::tryFromString returns Undefined for non-v4 or invalid UUID', function (): void {
    // v1 example (13th char = 1), should fail for v4 type
    $u1 = StringUuidV4::tryFromString('550e8400-e29b-11d4-a716-446655440000');
    $u2 = StringUuidV4::tryFromString('not-a-uuid');

    expect($u1)->toBeInstanceOf(Undefined::class)
        ->and($u2)->toBeInstanceOf(Undefined::class);
});
