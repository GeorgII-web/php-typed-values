<?php

declare(strict_types=1);

use PhpTypedValues\Float\FloatStandard;

it('__toString proxies to toString for FloatType', function (): void {
    $v = new FloatStandard(1.5);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('1.5');
});
