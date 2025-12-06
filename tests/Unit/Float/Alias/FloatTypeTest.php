<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\FloatType;

it('FloatType::fromFloat returns FloatType instance', function (): void {
    $v = FloatType::fromFloat(1.25);

    expect($v)->toBeInstanceOf(FloatType::class)
        ->and($v::class)->toBe(FloatType::class)
        ->and($v->value())->toBe(1.25);
});

it('FloatType::fromString returns FloatType instance', function (): void {
    $v = FloatType::fromString('2.75');

    expect($v)->toBeInstanceOf(FloatType::class)
        ->and($v::class)->toBe(FloatType::class)
        ->and($v->toString())->toBe('2.75');
});
