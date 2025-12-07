<?php

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
echo Integer::tryFromString('127')->toString() . \PHP_EOL;
echo Integer::tryFromInt(127)->toString() . \PHP_EOL;

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
