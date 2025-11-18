<?php

/**
 * Artificial code usage, test psalm types.
 *
 * For example, PSALM will fail if the INT parameter is used in the POSITIVE-INT function parameter.
 */

require_once 'vendor/autoload.php';

use PhpTypedValues\Type\Integer\Integer;
use PhpTypedValues\Type\Integer\NonNegativeInt;
use PhpTypedValues\Type\Integer\PositiveInt;
use PhpTypedValues\Type\Integer\WeekDayInt;

/**
 * Integer.
 */
testInteger(Integer::fromInt(10)->value());
testPositiveInt(PositiveInt::fromInt(10)->value());
testNonNegativeInt(NonNegativeInt::fromInt(10)->value());
testWeekDayInt(WeekDayInt::fromInt(7)->value());

echo Integer::fromString('10')->toString();

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
