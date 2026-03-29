<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\MariaDb\MediumText;

covers(MediumText::class);

describe('MediumText', function () {
    it('MediumText::fromString returns MediumText instance (late static binding)', function (): void {
        $val = 'medium text content';
        $v = MediumText::fromString($val);

        expect($v)->toBeInstanceOf(MediumText::class)
            ->and($v::class)->toBe(MediumText::class)
            ->and($v->value())->toBe($val);
    });
});
