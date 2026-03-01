<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Alias\Specific;

use PhpTypedValues\String\Alias\Specific\IpV4;

describe('IpV4', function () {
    it('IpV4::fromString returns IpV4 instance (late static binding)', function (): void {
        $ip = '127.0.0.1';
        $v = IpV4::fromString($ip);

        expect($v)->toBeInstanceOf(IpV4::class)
            ->and($v::class)->toBe(IpV4::class)
            ->and($v->value())->toBe($ip);
    });
});
