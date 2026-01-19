<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\NonNegative;

it('NonNegativeFloat::fromFloat returns NonNegativeFloat instance', function (): void {
    $v = NonNegative::fromFloat(0);

    expect($v)->toBeInstanceOf(NonNegative::class)
        ->and($v::class)->toBe(NonNegative::class)
        ->and($v->value())->toBe(0.0);
});

it('NonNegativeFloat::fromString returns NonNegativeFloat instance', function (): void {
    $v = NonNegative::fromString('2.0');

    expect($v)->toBeInstanceOf(NonNegative::class)
        ->and($v::class)->toBe(NonNegative::class)
        ->and($v->toString())->toBe('2.0');
});
