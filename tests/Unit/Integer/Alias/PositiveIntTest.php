<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\PositiveInt;

it('PositiveInt alias factories return PositiveInt instance', function (): void {
    $a = PositiveInt::fromInt(5);
    $b = PositiveInt::fromString('7');

    expect($a)->toBeInstanceOf(PositiveInt::class)
        ->and($a::class)->toBe(PositiveInt::class)
        ->and($a->value())->toBe(5)
        ->and($b)->toBeInstanceOf(PositiveInt::class)
        ->and($b->value())->toBe(7);
});
