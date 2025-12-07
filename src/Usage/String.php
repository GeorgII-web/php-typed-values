<?php

use PhpTypedValues\String\Alias\JsonStr;
use PhpTypedValues\String\Alias\NonEmptyStr;
use PhpTypedValues\String\Alias\Str;
use PhpTypedValues\String\Alias\StrType;
use PhpTypedValues\String\Json;
use PhpTypedValues\String\MariaDb\StringVarChar255;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\String\StringUuidV4;
use PhpTypedValues\String\StringUuidV7;

/**
 * String.
 */
testString(StringStandard::fromString('hi')->value());
testNonEmptyString(StringNonEmpty::fromString('hi')->value());

echo StringStandard::fromString('hi')->toString() . \PHP_EOL;
echo NonEmptyStr::fromString('hi')->toString() . \PHP_EOL;
echo StrType::fromString('hi')->toString() . \PHP_EOL;
echo Str::fromString('hi')->toString() . \PHP_EOL;
echo StringNonEmpty::tryFromString('hi')->toString() . \PHP_EOL;
echo StringStandard::tryFromString('hi')->toString() . \PHP_EOL;
echo StringUuidV4::tryFromString('550e8400-e29b-41d4-a716-446655440000')->toString() . \PHP_EOL;
echo StringUuidV7::tryFromString('01890f2a-5bcd-7def-8abc-1234567890ab')->toString() . \PHP_EOL;
echo StringVarChar255::tryFromString('hi')->toString() . \PHP_EOL;

// JSON
echo json_encode(JsonStr::fromString('{"a": 1, "b": "hi"}')->toArray(), \JSON_THROW_ON_ERROR) . \PHP_EOL;
echo json_encode(Json::fromString('{"a": 1}')->toObject(), \JSON_THROW_ON_ERROR) . \PHP_EOL;
echo Json::tryFromString('{}')->toString() . \PHP_EOL;

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
