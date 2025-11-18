<?php

declare(strict_types=1);

use PhpTypedValues\Type\Integer\NonNegativeInt;

it('creates NonNegativeInt', function (): void {
    expect((new NonNegativeInt(0))->value())->toBe(0);
});

it('fails on negatives', function (): void {
    expect(fn() => NonNegativeInt::fromInt(-1))->toThrow(InvalidArgumentException::class);
});
