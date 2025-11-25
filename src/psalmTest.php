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
use PhpTypedValues\Integer\IntegerBasic;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\WeekDayInt;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\String\StringBasic;

// try {
//    echo DateTimeImmutable::createFromFormat('U.u', '953402300800.000000')->format('U.u');
// } catch (Throwable $e) {
//    var_dump('error');
//    var_dump($e);
// }
//
// try {
//    echo TimestampMilliseconds::fromString('953402300800000')->toString(); // '253402300800000'
// } catch (Throwable $e) {
//    var_dump('error');
//    var_dump($e);
// }
// exit('ssssssssss');

/**
 * Integer.
 */
testInteger(IntegerBasic::fromInt(10)->value());
testPositiveInt(PositiveInt::fromInt(10)->value());
testNonNegativeInt(NonNegativeInt::fromInt(10)->value());
testWeekDayInt(WeekDayInt::fromInt(7)->value());

echo IntegerBasic::fromString('10')->toString() . \PHP_EOL;

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
