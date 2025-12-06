<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\NonNegativeFloat;

it('NonNegativeFloat::fromFloat returns NonNegativeFloat instance', function (): void {
    $v = NonNegativeFloat::fromFloat(0.0);

    expect($v)->toBeInstanceOf(NonNegativeFloat::class)
        ->and($v::class)->toBe(NonNegativeFloat::class)
        ->and($v->value())->toBe(0.0);
});

it('NonNegativeFloat::fromString returns NonNegativeFloat instance', function (): void {
    $v = NonNegativeFloat::fromString('2.0');

    expect($v)->toBeInstanceOf(NonNegativeFloat::class)
        ->and($v::class)->toBe(NonNegativeFloat::class)
        ->and($v->toString())->toBe('2');
});
