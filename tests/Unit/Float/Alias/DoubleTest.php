<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\DoubleType;

describe('DoubleType', function (): void {
    it('DoubleType::fromFloat returns Double instance (late static binding)', function (): void {
        $v = DoubleType::fromFloat(3.14);

        expect($v)->toBeInstanceOf(DoubleType::class)
            ->and($v::class)->toBe(DoubleType::class)
            ->and($v->value())->toBe(3.14);
    });

    it('DoubleType::fromString returns DoubleType instance (late static binding)', function (): void {
        $v = DoubleType::fromString('2.5');

        expect($v)->toBeInstanceOf(DoubleType::class)
            ->and($v::class)->toBe(DoubleType::class)
            ->and($v->toString())->toBe('2.5');
    });
});
