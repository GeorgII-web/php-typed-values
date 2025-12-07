<?php

declare(strict_types=1);

use PhpTypedValues\Exception\JsonStringTypeException;
use PhpTypedValues\String\StringJson;

it('constructs valid JSON via constructor', function (): void {
    $json = new StringJson('{"a":1,"b":[true,null,3.14]}');
    expect($json->value())->toBe('{"a":1,"b":[true,null,3.14]}')
        ->and($json->toString())->toBe('{"a":1,"b":[true,null,3.14]}');
});

it('creates from string factory with valid JSON', function (): void {
    $json = StringJson::fromString('"hello"');
    expect($json->value())->toBe('"hello"');
});

it('accepts top-level number/boolean/null JSON', function (): void {
    $n = StringJson::fromString('123');
    $t = StringJson::fromString('true');
    $nul = StringJson::fromString('null');
    expect($n->toString())->toBe('123')
        ->and($t->value())->toBe('true')
        ->and($nul->toString())->toBe('null');
});

it('throws on empty string', function (): void {
    expect(fn() => new StringJson(''))
        ->toThrow(JsonStringTypeException::class, 'String "" has no valid JSON value');
});

it('throws on invalid JSON string via constructor', function (): void {
    expect(fn() => new StringJson('{a:1}'))
        ->toThrow(JsonStringTypeException::class, 'String "{a:1}" has no valid JSON value');
});

it('throws on invalid JSON string via fromString', function (): void {
    expect(fn() => StringJson::fromString('{"a": }'))
        ->toThrow(JsonStringTypeException::class, 'String "{"a": }" has no valid JSON value');
});

it('decodes to stdClass via toObject', function (): void {
    $json = StringJson::fromString('{"name":"Alice","age":30,"flags":[true,false]}');
    $obj = $json->toObject();

    expect($obj)->toBeObject()
        ->and($obj->name)->toBe('Alice')
        ->and($obj->age)->toBe(30)
        ->and($obj->flags)->toBeArray()
        ->and($obj->flags)->toEqual([true, false]);
});

it('decodes to associative array via toArray', function (): void {
    $json = StringJson::fromString('{"a":1,"b":{"c":[1,2,3]},"d":null}');
    $arr = $json->toArray();

    expect($arr)->toBeArray()
        ->and($arr['a'])->toBe(1)
        ->and($arr['b'])->toBeArray()
        ->and($arr['b']['c'])->toEqual([1, 2, 3])
        ->and($arr['d'])->toBeNull();
});

it('exception code is 0 for invalid JSON via constructor', function (): void {
    try {
        new StringJson('{a:1}');
        expect()->fail('Expected exception not thrown');
    } catch (JsonStringTypeException $e) {
        expect($e->getCode())->toBe(0);
    }
});

it('exception code is 0 for invalid JSON via fromString', function (): void {
    try {
        StringJson::fromString('{"a": }');
        expect()->fail('Expected exception not thrown');
    } catch (JsonStringTypeException $e) {
        expect($e->getCode())->toBe(0);
    }
});
