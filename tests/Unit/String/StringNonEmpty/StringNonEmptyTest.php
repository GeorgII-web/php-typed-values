<?php

declare(strict_types=1);

use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

it('StringNonEmpty::tryFromString returns value for non-empty string', function (): void {
    $v = StringNonEmpty::tryFromString('abc');

    expect($v)
        ->toBeInstanceOf(StringNonEmpty::class)
        ->and($v->value())
        ->toBe('abc');
});

it('StringNonEmpty::tryFromString returns Undefined for empty string', function (): void {
    $u = StringNonEmpty::tryFromString('');

    expect($u)->toBeInstanceOf(Undefined::class);
});
