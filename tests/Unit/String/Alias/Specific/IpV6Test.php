<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\IpV6;

describe('IpV6', function () {
    it('IpV6::fromString returns IpV6 instance (late static binding)', function (): void {
        $ip = '::1';
        $v = IpV6::fromString($ip);

        expect($v)->toBeInstanceOf(IpV6::class)
            ->and($v::class)->toBe(IpV6::class)
            ->and($v->value())->toBe($ip);
    });
});
