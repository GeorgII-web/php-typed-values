<?php

declare(strict_types=1);

use PhpTypedValues\String\Json;
use PhpTypedValues\Undefined\Alias\Undefined;

it('Json::tryFromString returns value for valid JSON string', function (): void {
    $json = '{"a":1}';
    $v = Json::tryFromString($json);

    expect($v)
        ->toBeInstanceOf(Json::class)
        ->and($v->value())
        ->toBe($json);
});

it('Json::tryFromString returns Undefined for invalid JSON string', function (): void {
    $u = Json::tryFromString('{invalid');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('Json::toObject decodes valid JSON object and throws on invalid internal state', function (): void {
    $jsonText = '{"a":1,"b":2}';
    $j = Json::tryFromString($jsonText);

    // success branch
    \assert($j instanceof Json);
    $obj = $j->toObject();
    expect($obj)->toBeObject()
        ->and($obj->a)->toBe(1)
        ->and($obj->b)->toBe(2);
});

it('Json::toArray decodes valid JSON object as array and throws on invalid internal state', function (): void {
    $jsonText = '{"x":10,"y":20}';
    $j = Json::tryFromString($jsonText);
    \assert($j instanceof Json);

    // success branch
    $arr = $j->toArray();
    expect($arr)->toBeArray()
        ->and($arr['x'])->toBe(10)
        ->and($arr['y'])->toBe(20);
});
