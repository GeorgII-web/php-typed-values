<?php

declare(strict_types=1);

use PhpTypedValues\Exception\JsonStringTypeException;
use PhpTypedValues\String\StringJson;
use PhpTypedValues\Undefined\Alias\Undefined;

it('Json::tryFromString returns value for valid JSON string', function (): void {
    $json = '{"a":1}';
    $v = StringJson::tryFromString($json);

    expect($v)
        ->toBeInstanceOf(StringJson::class)
        ->and($v->value())
        ->toBe($json);
});

it('Json::tryFromString returns Undefined for invalid JSON string', function (): void {
    $u = StringJson::tryFromString('{invalid');

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('Json::toObject decodes valid JSON object and throws on invalid internal state', function (): void {
    $jsonText = '{"a":1,"b":2}';
    $j = StringJson::tryFromString($jsonText);

    // success branch
    \assert($j instanceof StringJson);
    $obj = $j->toObject();
    expect($obj)->toBeObject()
        ->and($obj->a)->toBe(1)
        ->and($obj->b)->toBe(2);
});

it('Json::toArray decodes valid JSON object as array and throws on invalid internal state', function (): void {
    $jsonText = '{"x":10,"y":20}';
    $j = StringJson::tryFromString($jsonText);
    \assert($j instanceof StringJson);

    // success branch
    $arr = $j->toArray();
    expect($arr)->toBeArray()
        ->and($arr['x'])->toBe(10)
        ->and($arr['y'])->toBe(20);
});

it('constructor throws with code 0 and previous JsonException on invalid JSON', function (): void {
    $invalid = '{invalid}';

    try {
        new StringJson($invalid);
        expect()->fail('Exception was not thrown');
    } catch (Throwable $e) {
        expect($e)
            ->toBeInstanceOf(JsonStringTypeException::class)
            ->and($e->getMessage())
            ->toBe(\sprintf('String "%s" has no valid JSON value', $invalid))
            ->and($e->getCode())
            ->toBe(0)
            ->and($e->getPrevious())
            ->toBeInstanceOf(JsonException::class);
    }
});

it('jsonSerialize returns string', function (): void {
    expect(StringJson::tryFromString('{}')->jsonSerialize())->toBeString();
});

it('__toString returns the original JSON text', function (): void {
    $json = '{"k":1}';
    $j = new StringJson($json);

    expect((string) $j)->toBe($json)
        ->and($j->__toString())->toBe($json);
});

it('tryFromMixed handles valid JSON text, stringable, and invalid mixed inputs', function (): void {
    // valid JSON as string
    $ok = StringJson::tryFromMixed('{"a":1}');

    // stringable producing valid JSON
    $json = '{"x":10}';
    $stringable = new class($json) {
        public function __construct(private string $v)
        {
        }

        public function __toString(): string
        {
            return $this->v;
        }
    };
    $fromStringable = StringJson::tryFromMixed($stringable);

    // invalid inputs
    $bad = StringJson::tryFromMixed('{invalid');
    $fromArray = StringJson::tryFromMixed(['x']);
    $fromNull = StringJson::tryFromMixed(null);

    expect($ok)->toBeInstanceOf(StringJson::class)
        ->and($ok->value())->toBe('{"a":1}')
        ->and($fromStringable)->toBeInstanceOf(StringJson::class)
        ->and($fromStringable->value())->toBe($json)
        ->and($bad)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringJson', function (): void {
    $j = new StringJson('{"a":1}');
    expect($j->isEmpty())->toBeFalse();
});
