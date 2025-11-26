<?php

/**
 * Artificial code usage, test psalm types.
 *
 * For example, PSALM will fail if the INT parameter is used in the POSITIVE-INT function parameter.
 */

require_once 'vendor/autoload.php';

use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\DateTime\DateTimeRFC3339;
use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;
use PhpTypedValues\Float\FloatBasic;
use PhpTypedValues\Float\NonNegativeFloat;
use PhpTypedValues\Integer\Alias\Id;
use PhpTypedValues\Integer\Alias\NonNegativeInt;
use PhpTypedValues\Integer\Alias\PositiveInt;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Integer\IntegerStandart;
use PhpTypedValues\Integer\IntegerWeekDay;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\String\StringBasic;

/**
 * Integer.
 */
testInteger(IntegerStandart::fromInt(10)->value());
testPositiveInt(IntegerPositive::fromInt(10)->value());
testNonNegativeInt(IntegerNonNegative::fromInt(10)->value());
testWeekDayInt(IntegerWeekDay::fromInt(7)->value());

echo NonNegativeInt::fromString('10')->toString() . \PHP_EOL;
echo PositiveInt::fromString('10')->toString() . \PHP_EOL;
echo IntegerStandart::fromString('10')->toString() . \PHP_EOL;
echo Id::fromString('10')->toString() . \PHP_EOL;

/**
 * String.
 */
testString(StringBasic::fromString('hi')->value());
testNonEmptyString(NonEmptyStr::fromString('hi')->value());

echo StringBasic::fromString('hi')->toString() . \PHP_EOL;

/**
 * Float.
 */
testFloat(FloatBasic::fromFloat(3.14)->value());

echo FloatBasic::fromString('2.71828')->toString() . \PHP_EOL;

// PositiveFloat usage
testPositiveFloat(NonNegativeFloat::fromFloat(0.5)->value());
echo NonNegativeFloat::fromString('3.14159')->toString() . \PHP_EOL;

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
