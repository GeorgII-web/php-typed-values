<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\Alias\EmptyStr;
use PhpTypedValues\String\Alias\MariaDb\Decimal;
use PhpTypedValues\String\Alias\MariaDb\Text;
use PhpTypedValues\String\Alias\MariaDb\VarChar255;
use PhpTypedValues\String\Alias\NonBlank;
use PhpTypedValues\String\Alias\NonEmpty;
use PhpTypedValues\String\Alias\Specific\CountryCode;
use PhpTypedValues\String\Alias\Specific\Email;
use PhpTypedValues\String\Alias\Specific\File;
use PhpTypedValues\String\Alias\Specific\Json;
use PhpTypedValues\String\Alias\Specific\Path;
use PhpTypedValues\String\Alias\Specific\Url;
use PhpTypedValues\String\Alias\Specific\UuidV4;
use PhpTypedValues\String\Alias\Specific\UuidV7;
use PhpTypedValues\String\Alias\Str;
use PhpTypedValues\String\Alias\StringType;
use PhpTypedValues\String\MariaDb\StringDecimal;
use PhpTypedValues\String\MariaDb\StringText;
use PhpTypedValues\String\MariaDb\StringVarChar255;
use PhpTypedValues\String\Specific\StringCountryCode;
use PhpTypedValues\String\Specific\StringEmail;
use PhpTypedValues\String\Specific\StringFileName;
use PhpTypedValues\String\Specific\StringMd5;
use PhpTypedValues\String\Specific\StringPath;
use PhpTypedValues\String\Specific\StringUrl;
use PhpTypedValues\String\Specific\StringUuidV4;
use PhpTypedValues\String\Specific\StringUuidV7;
use PhpTypedValues\String\StringEmpty;
use PhpTypedValues\String\StringNonBlank;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\Undefined;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

/**
 * String.
 */
echo PHP_EOL . '> STRING' . PHP_EOL;

testString(StringStandard::fromString('hi')->value());
testNonEmptyString(StringNonEmpty::fromString('hi')->value());
testEmptyString(StringEmpty::fromString('')->value());

echo EmptyStr::fromString('')->toString() . PHP_EOL;
echo StringMd5::hash('hi')->toString() . PHP_EOL;
echo StringStandard::fromString('hi')->toString() . PHP_EOL;
echo NonEmpty::fromString('hi')->toString() . PHP_EOL;
echo StringType::fromString('hi')->toString() . PHP_EOL;
echo Str::fromString('hi')->toString() . PHP_EOL;
echo StringNonEmpty::tryFromString('hi')->toString() . PHP_EOL;
echo StringStandard::tryFromString('hi')->toString() . PHP_EOL;
// NonBlank usage (valid and try*)
echo NonBlank::fromString(' hi ')->toString() . PHP_EOL;
echo StringNonBlank::fromString(' hi ')->toString() . PHP_EOL;

$file = StringFileName::fromString('file.name');
echo $file->toString() . ' ' . $file->getFileNameOnly() . ' ' . $file->getExtension() . PHP_EOL;

echo File::fromString('file.name')->toString() . PHP_EOL;
echo Path::fromString('/src/String')->toString() . PHP_EOL;
echo StringPath::fromString('src\String\\')->toString() . PHP_EOL;

echo UuidV4::tryFromString('550e8400-e29b-41d4-a716-446655440000')->toString() . PHP_EOL;

try {
    echo UuidV7::tryFromString('550e8400-e29b-41d4-a716-446655440000')->toString() . PHP_EOL;
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}
echo StringUuidV4::tryFromString('550e8400-e29b-41d4-a716-446655440000')->toString() . PHP_EOL;
try {
    echo StringUuidV7::tryFromString('01890f2a-5bcd-7def-8abc-1234567890ab')->toString() . PHP_EOL;
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}
echo StringVarChar255::tryFromString('hi')->toString() . PHP_EOL;
echo VarChar255::tryFromString('hi')->toString() . PHP_EOL;

// MariaDb TEXT
echo Text::fromString('lorem ipsum')->toString() . PHP_EOL;
echo StringText::fromString('lorem ipsum')->toString() . PHP_EOL;
$text = StringText::tryFromString(str_repeat('a', 10));
if (!($text instanceof Undefined)) {
    echo $text->toString() . PHP_EOL;
}

// JSON
echo json_encode(Json::fromString('{"a": 1, "b": "hi"}')->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;
echo json_encode(Json::fromString('{"a": 1}')->toObject(), JSON_THROW_ON_ERROR) . PHP_EOL;
echo Json::tryFromString('{}')->toString() . PHP_EOL;

// Email (usage and try* for Psalm visibility)
echo Email::fromString('User@Example.COM')->toString() . PHP_EOL; // normalized to lowercase
echo StringEmail::fromString('User@Example.COM')->toString() . PHP_EOL; // normalized to lowercase
$em = StringEmail::tryFromString('not-an-email');
if (!$em->isUndefined()) {
    echo $em->toString() . PHP_EOL;
}
if (!$em->isEmpty()) {
    echo $em->toString() . PHP_EOL;
}

// URL (usage and try* for Psalm visibility)
echo Url::fromString('https://example.com/path?x=1')->toString() . PHP_EOL;
echo StringUrl::fromString('https://example.com/path?x=1')->toString() . PHP_EOL;
$url = StringUrl::tryFromString('notaurl');
if (!($url instanceof Undefined)) {
    echo $url->toString() . PHP_EOL;
}

// CountryCode (usage and try* for Psalm visibility)
echo CountryCode::fromString('US')->toString() . PHP_EOL;
try {
    echo StringCountryCode::fromString('gb')->toString() . PHP_EOL; // normalized to uppercase
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}
$cc = StringCountryCode::tryFromString('ZZ');
if (!($cc instanceof Undefined)) {
    echo $cc->toString() . PHP_EOL;
}

echo StringStandard::fromString('no')->isTypeOf(\PhpTypedValues\Base\Primitive\String\StringTypeAbstractAbstract::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;

// MariaDb Decimal (usage and try*) and toFloat strictness
echo Decimal::fromString('3.14')->toString() . PHP_EOL;
echo StringDecimal::fromString('3.14')->toString() . PHP_EOL;
// tryFromString branch
$dec = StringDecimal::tryFromString('1.5');
if (!($dec instanceof Undefined)) {
    // toFloat may throw if string cannot be represented exactly; suppress for usage demo
    try {
        echo (string) $dec->toFloat() . PHP_EOL;
    } catch (TypeException) {
        // ignore in usage
    }
}

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

function testEmptyString(string $i): string
{
    return $i;
}
