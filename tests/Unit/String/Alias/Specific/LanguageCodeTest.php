<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\LanguageCode;

describe('LanguageCode', function () {
    it('LanguageCode::fromString returns LanguageCode instance (late static binding)', function (): void {
        $code = 'en';
        $v = LanguageCode::fromString($code);

        expect($v)->toBeInstanceOf(LanguageCode::class)
            ->and($v::class)->toBe(LanguageCode::class)
            ->and($v->value())->toBe($code);
    });
});
