<?php

namespace App\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Integer\Alias\Id;
use PhpTypedValues\Integer\Alias\Integer;
use PhpTypedValues\Integer\Alias\IntType;
use PhpTypedValues\Integer\Alias\MariaDb\Tiny;
use PhpTypedValues\Integer\Alias\NonNegative;
use PhpTypedValues\Integer\Alias\Positive;
use PhpTypedValues\Integer\Alias\WeekDay;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Integer\IntegerWeekDay;
use PhpTypedValues\Integer\MariaDb\IntegerTiny;

/**
 * Integer.
 */
echo PHP_EOL . '> INTEGER' . PHP_EOL;

testInteger(IntegerStandard::fromInt(10)->value());
testPositiveInt(IntegerPositive::fromInt(10)->value());
testNonNegativeInt(IntegerNonNegative::fromInt(10)->value());
testWeekDayInt(IntegerWeekDay::fromInt(7)->value());
echo WeekDay::fromInt(7)->value() . PHP_EOL;

// DB tinyint usage
echo Tiny::tryFromMixed(-5)->toString() . PHP_EOL;
echo Tiny::fromInt(-5)->toString() . PHP_EOL;
echo Tiny::fromInt(-5)->jsonSerialize() . PHP_EOL;
echo IntegerTiny::fromInt(-5)->toString() . PHP_EOL;
echo IntegerTiny::fromString('127')->toString() . PHP_EOL;
echo NonNegative::fromString('10')->toString() . PHP_EOL;
echo Positive::fromString('10')->toString() . PHP_EOL;
echo IntegerStandard::fromString('10')->toString() . PHP_EOL;
echo Id::fromString('10')->toString() . PHP_EOL;
echo IntType::fromString('10')->toString() . PHP_EOL;
echo Integer::fromString('10')->toString() . PHP_EOL;
echo Integer::tryFromString('127')->toString() . PHP_EOL;
echo Integer::tryFromInt(127)->toString() . PHP_EOL;
echo IntegerNonNegative::tryFromString('10')->toString() . PHP_EOL;
echo IntegerPositive::tryFromString('10')->toString() . PHP_EOL;
echo IntegerWeekDay::tryFromString('5')->toString() . PHP_EOL;
echo IntegerTiny::tryFromString('1')->toString() . PHP_EOL;

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
