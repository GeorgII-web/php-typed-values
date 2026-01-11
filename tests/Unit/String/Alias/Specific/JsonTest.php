<?php

declare(strict_types=1);

use PhpTypedValues\String\Alias\Specific\Json;

it('JsonStr::fromString returns JsonStr instance (late static binding)', function (): void {
    $json = '{"a":1,"b":"x"}';
    $v = Json::fromString($json);

    expect($v)->toBeInstanceOf(Json::class)
        ->and($v::class)->toBe(Json::class)
        ->and($v->value())->toBe($json);
});
