<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\Positive;

it('PositiveInt alias factories return PositiveInt instance', function (): void {
    $a = Positive::fromInt(5);
    $b = Positive::fromString('7');

    expect($a)->toBeInstanceOf(Positive::class)
        ->and($a::class)->toBe(Positive::class)
        ->and($a->value())->toBe(5)
        ->and($b)->toBeInstanceOf(Positive::class)
        ->and($b->value())->toBe(7);
});
