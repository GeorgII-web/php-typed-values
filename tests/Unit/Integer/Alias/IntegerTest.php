<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\IntegerType;

it('IntegerType alias fromInt returns IntegerType alias instance', function (): void {
    $v = IntegerType::fromInt(42);

    expect($v)->toBeInstanceOf(IntegerType::class)
        ->and($v::class)->toBe(IntegerType::class)
        ->and($v->value())->toBe(42);
});

it('IntegerType alias fromString returns IntegerType alias instance', function (): void {
    $v = IntegerType::fromString('7');

    expect($v)->toBeInstanceOf(IntegerType::class)
        ->and($v::class)->toBe(IntegerType::class)
        ->and($v->toString())->toBe('7');
});
