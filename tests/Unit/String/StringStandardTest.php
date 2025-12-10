<?php

declare(strict_types=1);

use PhpTypedValues\String\StringStandard;

it('StringStandard::tryFromString returns instance for any string', function (): void {
    $v = StringStandard::tryFromString('hello');

    expect($v)
        ->toBeInstanceOf(StringStandard::class)
        ->and($v->value())
        ->toBe('hello')
        ->and($v->toString())
        ->toBe('hello');
});

it('jsonSerialize returns string', function (): void {
    expect(StringStandard::tryFromString('hello')->jsonSerialize())->toBeString();
});
