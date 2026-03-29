<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\Md5;

covers(Md5::class);

describe('Md5', function () {
    it('Md5::fromString returns Md5 instance (late static binding)', function (): void {
        $hash = '5d41402abc4b2a76b9719d911017c592';
        $v = Md5::fromString($hash);

        expect($v)->toBeInstanceOf(Md5::class)
            ->and($v::class)->toBe(Md5::class)
            ->and($v->value())->toBe($hash);
    });
});
