<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\NonEmpty;

covers(NonEmpty::class);

describe('NonEmptyStr', function () {
    it('NonEmptyStr::fromString returns NonEmptyStr instance (late static binding)', function (): void {
        $v = NonEmpty::fromString('x');

        expect($v)->toBeInstanceOf(NonEmpty::class)
            ->and($v::class)->toBe(NonEmpty::class)
            ->and($v->value())->toBe('x');
    });
});
