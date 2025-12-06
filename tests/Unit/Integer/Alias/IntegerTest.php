<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\Integer;

it('Integer alias fromInt returns Integer alias instance', function (): void {
    $v = Integer::fromInt(42);

    expect($v)->toBeInstanceOf(Integer::class)
        ->and($v::class)->toBe(Integer::class)
        ->and($v->value())->toBe(42);
});

it('Integer alias fromString returns Integer alias instance', function (): void {
    $v = Integer::fromString('7');

    expect($v)->toBeInstanceOf(Integer::class)
        ->and($v::class)->toBe(Integer::class)
        ->and($v->toString())->toBe('7');
});
