<?php

namespace App\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Float\Alias\Double;
use PhpTypedValues\Float\Alias\FloatType;
use PhpTypedValues\Float\Alias\NonNegative;
use PhpTypedValues\Float\Alias\Positive;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Float.
 */
echo PHP_EOL . '> FLOAT' . PHP_EOL;

testFloat(FloatStandard::fromFloat(3.14)->value());

echo FloatStandard::fromString('2.71828')->toString() . PHP_EOL;
echo FloatStandard::tryFromFloat(2.71828)->toString() . PHP_EOL;
echo FloatNonNegative::tryFromMixed('2.71828')->toString() . PHP_EOL;
echo FloatNonNegative::tryFromFloat(2.71828)->toString() . PHP_EOL;
echo NonNegative::fromString('2.71828')->toString() . PHP_EOL;
echo FloatType::fromString('2.71828')->toString() . PHP_EOL;
echo Double::fromString('2.71828')->toString() . PHP_EOL;
echo Positive::fromString('2.8')->toString() . PHP_EOL;
echo FloatStandard::tryFromMixed('2.8')->toString() . PHP_EOL;

// PositiveFloat usage
testPositiveFloat(FloatNonNegative::fromFloat(0.5)->value());
echo FloatNonNegative::fromString('3.14159')->toString() . PHP_EOL;

// try* usages to satisfy Psalm (ensure both success and failure branches are referenced)
$ts = FloatStandard::tryFromString('1.23');
if (!($ts instanceof Undefined)) {
    echo $ts->toString() . PHP_EOL;
}

$ti = FloatPositive::tryFromFloat(2.2);
if (!($ti instanceof Undefined)) {
    echo $ti->toString() . PHP_EOL;
}

$tn = FloatNonNegative::tryFromString('-1'); // will likely be Undefined
if (!($tn instanceof Undefined)) {
    echo $tn->toString() . PHP_EOL;
}

/**
 * Artificial functions.
 */
function testFloat(float $f): float
{
    return $f;
}

function testPositiveFloat(float $f): float
{
    return $f;
}
