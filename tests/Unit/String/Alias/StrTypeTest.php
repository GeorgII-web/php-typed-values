<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\StringType;

describe('StrType', function () {
    it('StrType::fromString returns StrType instance (late static binding)', function (): void {
        $v = StringType::fromString('hello');

        expect($v)->toBeInstanceOf(StringType::class)
            ->and($v::class)->toBe(StringType::class)
            ->and($v->value())->toBe('hello');
    });
});
