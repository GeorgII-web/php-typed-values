<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\JsonStr;

it('JsonStr::fromString returns JsonStr instance (late static binding)', function (): void {
    $json = '{"a":1,"b":"x"}';
    $v = JsonStr::fromString($json);

    expect($v)->toBeInstanceOf(JsonStr::class)
        ->and($v::class)->toBe(JsonStr::class)
        ->and($v->value())->toBe($json);
});
