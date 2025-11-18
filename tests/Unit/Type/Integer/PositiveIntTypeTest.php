<?php

declare(strict_types=1);

use PhpTypedValues\Exception\IntegerTypeException;
use PhpTypedValues\Type\Integer\PositiveInt;

it('PositiveInt accepts >0 and rejects 0 or negatives', function (): void {
    expect((new PositiveInt(1))->value())->toBe(1);
    expect(fn() => new PositiveInt(0))->toThrow(IntegerTypeException::class);
    expect(fn() => new PositiveInt(-1))->toThrow(IntegerTypeException::class);
});
