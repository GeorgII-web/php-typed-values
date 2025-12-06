<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\Double;

it('Double::fromFloat returns Double instance (late static binding)', function (): void {
    $v = Double::fromFloat(3.14);

    expect($v)->toBeInstanceOf(Double::class)
        ->and($v::class)->toBe(Double::class)
        ->and($v->value())->toBe(3.14);
});

it('Double::fromString returns Double instance (late static binding)', function (): void {
    $v = Double::fromString('2.5');

    expect($v)->toBeInstanceOf(Double::class)
        ->and($v::class)->toBe(Double::class)
        ->and($v->toString())->toBe('2.5');
});
