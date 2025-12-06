<?php

/**
 * Artificial code usage, test psalm types.
 *
 * For example, PSALM will fail if the INT parameter is used in the POSITIVE-INT function parameter.
 */

require_once 'vendor/autoload.php';

use PhpTypedValues\Abstract\Bool\BoolTypeInterface;
use PhpTypedValues\Bool\Alias\Boolean;
use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\DateTime\DateTimeRFC3339;
use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Float\Alias\Double;
use PhpTypedValues\Float\Alias\FloatType;
use PhpTypedValues\Float\Alias\NonNegativeFloat;
use PhpTypedValues\Float\Alias\PositiveFloat;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Integer\Alias\Id;
use PhpTypedValues\Integer\Alias\Integer;
use PhpTypedValues\Integer\Alias\IntType;
use PhpTypedValues\Integer\Alias\NonNegativeInt;
use PhpTypedValues\Integer\Alias\PositiveInt;
use PhpTypedValues\Integer\Alias\TinyInt;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Integer\IntegerWeekDay;
use PhpTypedValues\Integer\MariaDb\IntTiny;
use PhpTypedValues\String\Alias\JsonStr;
use PhpTypedValues\String\Alias\NonEmptyStr;
use PhpTypedValues\String\Alias\Str;
use PhpTypedValues\String\Alias\StrType;
use PhpTypedValues\String\Json;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\String\StringStandard;
use PhpTypedValues\Undefined\Alias\NotExist;
use PhpTypedValues\Undefined\Alias\NotFound;
use PhpTypedValues\Undefined\Alias\NotSet;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Undefined\Alias\Unknown;
use PhpTypedValues\Undefined\UndefinedStandard;

/**
 * Integer.
 */
testInteger(IntegerStandard::fromInt(10)->value());
testPositiveInt(IntegerPositive::fromInt(10)->value());
testNonNegativeInt(IntegerNonNegative::fromInt(10)->value());
testWeekDayInt(IntegerWeekDay::fromInt(7)->value());

// DB tinyint usage
echo TinyInt::fromInt(-5)->toString() . \PHP_EOL;
echo IntTiny::fromInt(-5)->toString() . \PHP_EOL;
echo IntTiny::fromString('127')->toString() . \PHP_EOL;

echo NonNegativeInt::fromString('10')->toString() . \PHP_EOL;
echo PositiveInt::fromString('10')->toString() . \PHP_EOL;
echo IntegerStandard::fromString('10')->toString() . \PHP_EOL;
echo Id::fromString('10')->toString() . \PHP_EOL;
echo IntType::fromString('10')->toString() . \PHP_EOL;
echo Integer::fromString('10')->toString() . \PHP_EOL;

/**
 * String.
 */
testString(StringStandard::fromString('hi')->value());
testNonEmptyString(StringNonEmpty::fromString('hi')->value());

echo StringStandard::fromString('hi')->toString() . \PHP_EOL;
echo NonEmptyStr::fromString('hi')->toString() . \PHP_EOL;
echo StrType::fromString('hi')->toString() . \PHP_EOL;
echo Str::fromString('hi')->toString() . \PHP_EOL;

/**
 * Float.
 */
testFloat(FloatStandard::fromFloat(3.14)->value());

echo FloatStandard::fromString('2.71828')->toString() . \PHP_EOL;
echo NonNegativeFloat::fromString('2.71828')->toString() . \PHP_EOL;
echo FloatType::fromString('2.71828')->toString() . \PHP_EOL;
echo Double::fromString('2.71828')->toString() . \PHP_EOL;
echo PositiveFloat::fromString('2.8')->toString() . \PHP_EOL;

// PositiveFloat usage
testPositiveFloat(FloatNonNegative::fromFloat(0.5)->value());
echo FloatNonNegative::fromString('3.14159')->toString() . \PHP_EOL;

/**
 * Boolean.
 */
echo BoolStandard::fromString('true')->toString() . \PHP_EOL;
echo BoolStandard::fromInt(1)->toString() . \PHP_EOL;
echo BoolStandard::fromBool(true)->toString() . \PHP_EOL;
echo Boolean::fromBool(Boolean::fromBool(true)->value())->toString() . \PHP_EOL;
// Ensure interface method usage is visible to Psalm
echo (testBool(BoolStandard::fromBool(true)) ? 'true' : 'false') . \PHP_EOL;

/**
 * DateTime.
 */
echo DateTimeAtom::getFormat() . \PHP_EOL;

$dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00')->value();
echo DateTimeAtom::fromDateTime($dt)->toString() . \PHP_EOL;

$dt = DateTimeRFC3339::fromString('2025-01-02T03:04:05+00:00')->value();
echo DateTimeRFC3339::fromDateTime($dt)->toString() . \PHP_EOL;

// Timestamp
$tsVo = TimestampSeconds::fromString('1735787045');
echo TimestampSeconds::fromDateTime($tsVo->value())->toString() . \PHP_EOL;

$tsVo = TimestampMilliseconds::fromString('1735787045123');
echo TimestampMilliseconds::fromDateTime($tsVo->value())->toString() . \PHP_EOL;

// JSON
echo json_encode(JsonStr::fromString('{"a": 1, "b": "hi"}')->toArray(), \JSON_THROW_ON_ERROR);
echo json_encode(Json::fromString('{"a": 1, "b": "hi"}')->toObject(), \JSON_THROW_ON_ERROR);

// Undefined
try {
    UndefinedStandard::create()->toString();
} catch (UndefinedTypeException $e) {
    // suppress
}
try {
    NotExist::create()->value();
} catch (UndefinedTypeException $e) {
    // suppress
}
NotFound::create();
NotSet::create();
Undefined::create();
Unknown::create();

/**
 * Artificial functions.
 */
function testInteger(int $i): int
{
    return $i;
}

/**
 * @param positive-int $i
 */
function testPositiveInt(int $i): int
{
    return $i;
}

/**
 * @param non-negative-int $i
 */
function testNonNegativeInt(int $i): int
{
    return $i;
}

/**
 * @param int<1, 7> $i
 */
function testWeekDayInt(int $i): int
{
    return $i;
}

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

function testFloat(float $f): float
{
    return $f;
}

function testPositiveFloat(float $f): float
{
    return $f;
}

/**
 * Exercise BoolTypeInterface::value() for Psalm.
 */
function testBool(BoolTypeInterface $b): bool
{
    return $b->value();
}
