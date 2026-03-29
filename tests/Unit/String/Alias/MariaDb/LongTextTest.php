<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\MariaDb\LongText;

covers(LongText::class);

describe('LongText', function () {
    it('LongText::fromString returns LongText instance (late static binding)', function (): void {
        $v = LongText::fromString('test');

        expect($v)->toBeInstanceOf(LongText::class)
            ->and($v::class)->toBe(LongText::class)
            ->and($v->value())->toBe('test');
    });
});
