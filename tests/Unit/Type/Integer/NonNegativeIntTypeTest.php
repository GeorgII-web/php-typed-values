<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Type\Integer\NonNegativeInt;

it('NonNegativeInt accepts zero and rejects negatives', function (): void {
    expect((new NonNegativeInt(0))->value())->toBe(0);
    expect(fn() => new NonNegativeInt(-1))->toThrow(IntegerTypeException::class);
});
