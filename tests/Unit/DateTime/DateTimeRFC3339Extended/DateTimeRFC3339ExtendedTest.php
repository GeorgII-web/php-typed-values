<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeRFC3339Extended;
use PhpTypedValues\Undefined\Alias\Undefined;

it('DateTimeRFC3339Extended::tryFromString returns value for valid RFC3339_EXTENDED string', function (): void {
    $s = '2025-01-02T03:04:05.123456+00:00';
    $v = DateTimeRFC3339Extended::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(Undefined::class);
});

it('DateTimeRFC3339Extended::tryFromString returns Undefined for invalid string', function (): void {
    $u = DateTimeRFC3339Extended::tryFromString('2025-01-02T03:04:05+00:00');

    expect($u)->toBeInstanceOf(Undefined::class);
});
