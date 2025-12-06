<?php

require_once 'vendor/autoload.php';

use PhpTypedValues\Float\Alias\Double;
use PhpTypedValues\Float\Alias\FloatType;
use PhpTypedValues\Float\Alias\NonNegativeFloat;
use PhpTypedValues\Float\Alias\PositiveFloat;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Float\FloatStandard;

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
