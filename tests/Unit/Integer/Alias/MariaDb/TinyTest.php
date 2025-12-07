<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\MariaDb\Tiny;

it('Tiny alias factories return Tiny instance', function (): void {
    $a = Tiny::fromInt(10);
    $b = Tiny::fromString('-5');

    expect($a)->toBeInstanceOf(Tiny::class)
        ->and($a::class)->toBe(Tiny::class)
        ->and($a->value())->toBe(10)
        ->and($b)->toBeInstanceOf(Tiny::class)
        ->and($b->value())->toBe(-5);
});
