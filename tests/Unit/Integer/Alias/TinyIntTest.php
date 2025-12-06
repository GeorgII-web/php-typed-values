<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\TinyInt;

it('TinyInt alias factories return TinyInt instance', function (): void {
    $a = TinyInt::fromInt(10);
    $b = TinyInt::fromString('-5');

    expect($a)->toBeInstanceOf(TinyInt::class)
        ->and($a::class)->toBe(TinyInt::class)
        ->and($a->value())->toBe(10)
        ->and($b)->toBeInstanceOf(TinyInt::class)
        ->and($b->value())->toBe(-5);
});
