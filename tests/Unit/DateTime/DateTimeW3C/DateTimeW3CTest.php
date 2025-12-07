<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeW3C;
use PhpTypedValues\Undefined\Alias\Undefined;

it('DateTimeW3C::tryFromString returns value for valid W3C string', function (): void {
    $s = '2025-01-02T03:04:05+00:00';
    $v = DateTimeW3C::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeW3C::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeW3C::tryFromString returns Undefined for invalid string', function (): void {
    $u = DateTimeW3C::tryFromString('2025-01-02 03:04:05Z');

    expect($u)->toBeInstanceOf(Undefined::class);
});
