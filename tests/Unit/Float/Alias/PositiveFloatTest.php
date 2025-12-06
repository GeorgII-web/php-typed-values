<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\PositiveFloat;

it('PositiveFloat::fromFloat returns PositiveFloat instance', function (): void {
    $v = PositiveFloat::fromFloat(0.1);

    expect($v)->toBeInstanceOf(PositiveFloat::class)
        ->and($v::class)->toBe(PositiveFloat::class)
        ->and($v->value())->toBe(0.1);
});

it('PositiveFloat::fromString returns PositiveFloat instance', function (): void {
    $v = PositiveFloat::fromString('1.5');

    expect($v)->toBeInstanceOf(PositiveFloat::class)
        ->and($v::class)->toBe(PositiveFloat::class)
        ->and($v->toString())->toBe('1.5');
});
