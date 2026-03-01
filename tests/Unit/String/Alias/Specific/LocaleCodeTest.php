<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\LocaleCode;

describe('LocaleCode', function () {
    it('LocaleCode::fromString returns LocaleCode instance (late static binding)', function (): void {
        $code = 'en_US';
        $v = LocaleCode::fromString($code);

        expect($v)->toBeInstanceOf(LocaleCode::class)
            ->and($v::class)->toBe(LocaleCode::class)
            ->and($v->value())->toBe($code);
    });
});
