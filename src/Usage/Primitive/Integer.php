<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use PhpTypedValues\Base\Primitive\Undefined\UndefinedTypeAbstractAbstract;
use PhpTypedValues\Integer\Alias\IntegerType;
use PhpTypedValues\Integer\Alias\MariaDb\Tiny;
use PhpTypedValues\Integer\Alias\NonNegative;
use PhpTypedValues\Integer\Alias\Positive;
use PhpTypedValues\Integer\Alias\Specific\Id;
use PhpTypedValues\Integer\Alias\Specific\WeekDay;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\Integer\MariaDb\IntegerTiny;
use PhpTypedValues\Integer\Specific\IntegerWeekDay;
use PhpTypedValues\Undefined\Alias\Unknown;
use const PHP_EOL;

/**
 * Integer.
 */
echo PHP_EOL . '> INTEGER' . PHP_EOL;

testInteger(IntegerStandard::fromInt(10)->value());
testPositiveInt(IntegerPositive::fromInt(10)->value());
testNonNegativeInt(IntegerNonNegative::fromInt(10)->value());
testWeekDayInt(IntegerWeekDay::fromInt(7)->value());

echo IntegerStandard::tryFromString('no', Unknown::create())->isTypeOf(WeekDay::class, Tiny::class, UndefinedTypeAbstractAbstract::class) ? 'Type correct - "Unknown" is type of "UndefinedType"' . PHP_EOL : 'Invalid type' . PHP_EOL;
echo WeekDay::fromLabel('Monday')->toLabel() . PHP_EOL;
echo WeekDay::fromInt(7)->value() . PHP_EOL;
echo Tiny::tryFromMixed(-5)->toString() . PHP_EOL;
echo Tiny::fromInt(-5)->toString() . PHP_EOL;
echo Tiny::fromInt(-5)->jsonSerialize() . PHP_EOL;
echo IntegerTiny::fromInt(-5)->toString() . PHP_EOL;
echo IntegerTiny::fromString('127')->toString() . PHP_EOL;
echo NonNegative::fromString('10')->toString() . PHP_EOL;
echo Positive::fromString('10')->toString() . PHP_EOL;
echo IntegerStandard::fromString('10')->toString() . PHP_EOL;
echo Id::fromString('10')->toString() . PHP_EOL;
echo IntegerType::fromString('10')->toString() . PHP_EOL;
echo IntegerType::fromString('10')->toString() . PHP_EOL;
echo IntegerType::tryFromString('127')->toString() . PHP_EOL;
echo IntegerType::tryFromInt(127)->toString() . PHP_EOL;
echo IntegerStandard::fromInt(42)->toInt() . PHP_EOL;
echo IntegerNonNegative::fromInt(0)->toInt() . PHP_EOL;
echo IntegerPositive::fromInt(1)->toInt() . PHP_EOL;
echo IntegerWeekDay::fromInt(7)->toInt() . PHP_EOL;
echo IntegerTiny::fromInt(127)->toInt() . PHP_EOL;
echo IntegerNonNegative::tryFromString('10')->toString() . PHP_EOL;
echo IntegerPositive::tryFromString('10')->toString() . PHP_EOL;
echo IntegerWeekDay::tryFromString('5')->toString() . PHP_EOL;
echo IntegerTiny::tryFromString('1')->toString() . PHP_EOL;
echo (string) IntegerStandard::fromInt(42)->toFloat() . PHP_EOL;
echo (string) IntegerStandard::fromInt(42)->toBool() . PHP_EOL;

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
