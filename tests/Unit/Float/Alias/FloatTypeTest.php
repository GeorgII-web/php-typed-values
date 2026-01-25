<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\DoubleType;

describe('FloatType', function (): void {
    it('FloatType::fromFloat returns FloatType instance', function (): void {
        $v = DoubleType::fromFloat(1.25);

        expect($v)->toBeInstanceOf(DoubleType::class)
            ->and($v::class)->toBe(DoubleType::class)
            ->and($v->value())->toBe(1.25);
    });

    it('FloatType::fromString returns FloatType instance', function (): void {
        $v = DoubleType::fromString('2.75');

        expect($v)->toBeInstanceOf(DoubleType::class)
            ->and($v::class)->toBe(DoubleType::class)
            ->and($v->toString())->toBe('2.75');
    });
});
