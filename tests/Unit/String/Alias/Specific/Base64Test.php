<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\Base64;

covers(Base64::class);

describe('Base64', function () {
    it('Base64::fromString returns Base64 instance (late static binding)', function (): void {
        $encoded = 'SGVsbG8gV29ybGQ=';
        $v = Base64::fromString($encoded);

        expect($v)->toBeInstanceOf(Base64::class)
            ->and($v::class)->toBe(Base64::class)
            ->and($v->value())->toBe($encoded);
    });
});
