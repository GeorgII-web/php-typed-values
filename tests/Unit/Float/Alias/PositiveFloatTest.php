<?php

declare(strict_types=1);

use PhpTypedValues\Float\Alias\Positive;

describe('PositiveFloat', function (): void {
    it('PositiveFloat::fromFloat returns PositiveFloat instance', function (): void {
        $v = Positive::fromFloat(0.1);

        expect($v)->toBeInstanceOf(Positive::class)
            ->and($v::class)->toBe(Positive::class)
            ->and($v->value())->toBe(0.1);
    });

    it('PositiveFloat::fromString returns PositiveFloat instance', function (): void {
        $v = Positive::fromString('1.5');

        expect($v)->toBeInstanceOf(Positive::class)
            ->and($v::class)->toBe(Positive::class)
            ->and($v->toString())->toBe('1.5');
    });
});
