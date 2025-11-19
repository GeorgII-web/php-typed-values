<?php

/**
 * Artificial code usage, test psalm types.
 *
 * For example, PSALM will fail if the INT parameter is used in the POSITIVE-INT function parameter.
 */

require_once 'vendor/autoload.php';

use PhpTypedValues\Integer\Integer;
use PhpTypedValues\Integer\NonNegativeInt;
use PhpTypedValues\Integer\PositiveInt;
use PhpTypedValues\Integer\WeekDayInt;
use PhpTypedValues\String\NonEmptyStr;
use PhpTypedValues\String\Str;

/**
 * Integer.
 */
testInteger(Integer::fromInt(10)->value());
testPositiveInt(PositiveInt::fromInt(10)->value());
testNonNegativeInt(NonNegativeInt::fromInt(10)->value());
testWeekDayInt(WeekDayInt::fromInt(7)->value());

echo Integer::fromString('10')->toString();

/**
 * String.
 */
testString(Str::fromString('hi')->value());
testNonEmptyString(NonEmptyStr::fromString('hi')->value());

echo Str::fromString('hi')->toString();

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
