<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\NonNegativeInt;

it('NonNegativeInt alias factories return NonNegativeInt instance', function (): void {
    $a = NonNegativeInt::fromInt(0);
    $b = NonNegativeInt::fromString('12');

    expect($a)->toBeInstanceOf(NonNegativeInt::class)
        ->and($a::class)->toBe(NonNegativeInt::class)
        ->and($a->value())->toBe(0)
        ->and($b)->toBeInstanceOf(NonNegativeInt::class)
        ->and($b->value())->toBe(12);
});
