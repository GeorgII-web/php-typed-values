<?php

use PhpTypedValues\String\Alias\JsonStr;
use PhpTypedValues\String\Alias\NonEmptyStr;
use PhpTypedValues\String\Alias\Str;
use PhpTypedValues\String\Alias\StrType;
use PhpTypedValues\String\Json;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\String\StringStandard;

/**
 * String.
 */
testString(StringStandard::fromString('hi')->value());
testNonEmptyString(StringNonEmpty::fromString('hi')->value());

echo StringStandard::fromString('hi')->toString() . \PHP_EOL;
echo NonEmptyStr::fromString('hi')->toString() . \PHP_EOL;
echo StrType::fromString('hi')->toString() . \PHP_EOL;
echo Str::fromString('hi')->toString() . \PHP_EOL;

// JSON
echo json_encode(JsonStr::fromString('{"a": 1, "b": "hi"}')->toArray(), \JSON_THROW_ON_ERROR);
echo json_encode(Json::fromString('{"a": 1, "b": "hi"}')->toObject(), \JSON_THROW_ON_ERROR);

/**
 * Artificial functions.
 */
function testString(string $i): string
{
    return $i;
}

/**
 * @param non-empty-string $i
 */
function testNonEmptyString(string $i): string
{
    return $i;
}
