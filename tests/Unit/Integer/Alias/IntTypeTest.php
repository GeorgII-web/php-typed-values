<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\IntType;

it('IntType alias factories return IntType instance', function (): void {
    $a = IntType::fromInt(-5);
    $b = IntType::fromString('10');

    expect($a)->toBeInstanceOf(IntType::class)
        ->and($a::class)->toBe(IntType::class)
        ->and($a->value())->toBe(-5)
        ->and($b)->toBeInstanceOf(IntType::class)
        ->and($b->toString())->toBe('10');
});
