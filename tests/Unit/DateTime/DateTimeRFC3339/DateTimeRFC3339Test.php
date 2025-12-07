<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeRFC3339;
use PhpTypedValues\Undefined\Alias\Undefined;

it('DateTimeRFC3339::tryFromString returns value for valid RFC3339 string', function (): void {
    $s = '2025-01-02T03:04:05+00:00';
    $v = DateTimeRFC3339::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeRFC3339::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeRFC3339::tryFromString returns Undefined for invalid string', function (): void {
    // Missing timezone
    $u = DateTimeRFC3339::tryFromString('2025-01-02T03:04:05');

    expect($u)->toBeInstanceOf(Undefined::class);
});
