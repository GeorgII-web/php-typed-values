<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\Undefined\Alias\Undefined;

it('DateTimeAtom::tryFromString returns value for valid ATOM string', function (): void {
    $s = '2025-01-02T03:04:05+00:00';
    $v = DateTimeAtom::tryFromString($s);

    expect($v)
        ->toBeInstanceOf(DateTimeAtom::class)
        ->and($v->toString())
        ->toBe($s);
});

it('DateTimeAtom::tryFromString returns Undefined for invalid string', function (): void {
    // Missing timezone offset
    $u = DateTimeAtom::tryFromString('2025-01-02T03:04:05');

    expect($u)->toBeInstanceOf(Undefined::class);
});
