<?php

declare(strict_types=1);

use PhpTypedValues\Integer\Alias\IntegerType;

describe('IntegerType', function () {
    it('IntType alias factories return IntType instance', function (): void {
        $a = IntegerType::fromInt(-5);
        $b = IntegerType::fromString('10');

        expect($a)->toBeInstanceOf(IntegerType::class)
            ->and($a::class)->toBe(IntegerType::class)
            ->and($a->value())->toBe(-5)
            ->and($b)->toBeInstanceOf(IntegerType::class)
            ->and($b->toString())->toBe('10');
    });
});
