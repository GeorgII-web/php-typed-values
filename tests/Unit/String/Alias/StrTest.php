<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\Str;

describe('Str', function () {
    it('Str::fromString returns Str instance (late static binding)', function (): void {
        $v = Str::fromString('hello');

        expect($v)->toBeInstanceOf(Str::class)
            ->and($v::class)->toBe(Str::class)
            ->and($v->toString())->toBe('hello')
            ->and($v->value())->toBe('hello');
    });
});
