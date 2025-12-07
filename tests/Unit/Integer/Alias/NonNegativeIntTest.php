<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\NonNegative;

it('NonNegativeInt alias factories return NonNegativeInt instance', function (): void {
    $a = NonNegative::fromInt(0);
    $b = NonNegative::fromString('12');

    expect($a)->toBeInstanceOf(NonNegative::class)
        ->and($a::class)->toBe(NonNegative::class)
        ->and($a->value())->toBe(0)
        ->and($b)->toBeInstanceOf(NonNegative::class)
        ->and($b->value())->toBe(12);
});
