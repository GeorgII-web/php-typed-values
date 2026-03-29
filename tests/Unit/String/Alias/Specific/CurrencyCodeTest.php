<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\CurrencyCode;

covers(CurrencyCode::class);

describe('CurrencyCode', function () {
    it('CurrencyCode::fromString returns CurrencyCode instance (late static binding)', function (): void {
        $code = 'USD';
        $v = CurrencyCode::fromString($code);

        expect($v)->toBeInstanceOf(CurrencyCode::class)
            ->and($v::class)->toBe(CurrencyCode::class)
            ->and($v->value())->toBe($code);
    });
});
