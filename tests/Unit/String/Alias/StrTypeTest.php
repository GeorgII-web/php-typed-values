<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\StrType;

it('StrType::fromString returns StrType instance (late static binding)', function (): void {
    $v = StrType::fromString('hello');

    expect($v)->toBeInstanceOf(StrType::class)
        ->and($v::class)->toBe(StrType::class)
        ->and($v->value())->toBe('hello');
});
