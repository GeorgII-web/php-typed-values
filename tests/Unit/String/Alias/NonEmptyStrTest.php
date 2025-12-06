<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\NonEmptyStr;

it('NonEmptyStr::fromString returns NonEmptyStr instance (late static binding)', function (): void {
    $v = NonEmptyStr::fromString('x');

    expect($v)->toBeInstanceOf(NonEmptyStr::class)
        ->and($v::class)->toBe(NonEmptyStr::class)
        ->and($v->value())->toBe('x');
});
