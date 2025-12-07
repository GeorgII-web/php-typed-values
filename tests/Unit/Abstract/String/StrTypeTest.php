<?php

declare(strict_types=1);

use PhpTypedValues\String\StringStandard;

it('__toString proxies to toString for StrType', function (): void {
    $v = new StringStandard('abc');

    expect((string) $v)
        ->toBe($v->toString())
        ->and((string) $v)
        ->toBe('abc');
});
