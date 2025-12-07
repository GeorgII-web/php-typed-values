<?php

declare(strict_types=1);

use PhpTypedValues\Integer\IntegerStandard;

it('__toString proxies to toString for IntType', function (): void {
    $v = new IntegerStandard(123);

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('123');
});
