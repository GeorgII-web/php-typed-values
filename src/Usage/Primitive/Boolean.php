<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Base\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Bool\Alias\Binary;
use PhpTypedValues\Bool\Alias\BooleanType;
use PhpTypedValues\Bool\Alias\Flag;
use PhpTypedValues\Bool\Alias\Logical;
use PhpTypedValues\Bool\Alias\Toggle;
use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Bool\FalseStandard;
use PhpTypedValues\Bool\TrueStandard;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Boolean.
 */
echo PHP_EOL . '> BOOLEAN' . PHP_EOL;

$undefinedType1 = BoolStandard::tryFromInt(2);
$undefinedType2 = BoolStandard::tryFromString('test');
try {
    echo $undefinedType1->value();
    echo $undefinedType2->value();
} catch (UndefinedTypeException $e) {
    // suppress
}
echo BoolStandard::fromString('true')->toString() . PHP_EOL;
echo ((string) BoolStandard::fromString('true')->jsonSerialize()) . PHP_EOL;
echo BoolStandard::fromInt(1)->toString() . PHP_EOL;
echo BoolStandard::fromBool(true)->toString() . PHP_EOL;
echo BooleanType::fromBool(BooleanType::fromBool(true)->value())->toString() . PHP_EOL;
// Ensure interface method usage is visible to Psalm
echo (testBool(BoolStandard::fromBool(true)) ? 'true' : 'false') . PHP_EOL;
echo TrueStandard::tryFromMixed('true')->toString() . PHP_EOL;
echo Binary::tryFromMixed('true')->toString() . PHP_EOL;
echo Flag::tryFromMixed('true')->toString() . PHP_EOL;
echo Logical::tryFromMixed('true')->toString() . PHP_EOL;
echo Toggle::tryFromMixed('true')->toString() . PHP_EOL;

echo Toggle::fromString('true')->isTypeOf(Toggle::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;

// true/false literal usages (and try* to reference both branches for Psalm)
$t1 = TrueStandard::tryFromString('yes');
if (!($t1 instanceof Undefined)) {
    echo $t1->toString() . PHP_EOL;
}

$t2 = TrueStandard::tryFromInt(1);
if (!($t2 instanceof Undefined)) {
    echo $t2->toString() . PHP_EOL;
}

$f1 = FalseStandard::tryFromString('off');
if (!($f1 instanceof Undefined)) {
    echo $f1->toString() . PHP_EOL;
}

$f2 = FalseStandard::tryFromInt(0);
if (!($f2 instanceof Undefined)) {
    echo $f2->toString() . PHP_EOL;
}

// Add usage for the unused methods from BoolTypeInterface
echo '--- Interface method usages ---' . PHP_EOL;

// toBool() usage
echo 'toBool(): ' . (BoolStandard::fromString('true')->toBool() ? 'true' : 'false') . PHP_EOL;
echo 'toBool() from false: ' . (BoolStandard::fromString('false')->toBool() ? 'true' : 'false') . PHP_EOL;

// toFloat() usage
echo 'toFloat(): ' . ((string) BoolStandard::fromBool(true)->toFloat()) . PHP_EOL;
echo 'toFloat() from false: ' . ((string) BoolStandard::fromBool(false)->toFloat()) . PHP_EOL;

// toInt() usage
echo 'toInt(): ' . ((string) BoolStandard::fromBool(true)->toInt()) . PHP_EOL;
echo 'toInt() from false: ' . ((string) BoolStandard::fromBool(false)->toInt()) . PHP_EOL;

// tryFromBool() usage
$tryFromBoolResult1 = BoolStandard::tryFromBool(true);
if (!($tryFromBoolResult1 instanceof Undefined)) {
    echo 'tryFromBool(true): ' . $tryFromBoolResult1->toString() . PHP_EOL;
}

$tryFromBoolResult2 = BoolStandard::tryFromBool(false);
if (!($tryFromBoolResult2 instanceof Undefined)) {
    echo 'tryFromBool(false): ' . $tryFromBoolResult2->toString() . PHP_EOL;
}

// tryFromFloat() usage
$tryFromFloatResult1 = BoolStandard::tryFromFloat(1.0);
if (!($tryFromFloatResult1 instanceof Undefined)) {
    echo 'tryFromFloat(1.0): ' . $tryFromFloatResult1->toString() . PHP_EOL;
}

$tryFromFloatResult2 = BoolStandard::tryFromFloat(0.0);
if (!($tryFromFloatResult2 instanceof Undefined)) {
    echo 'tryFromFloat(0.0): ' . $tryFromFloatResult2->toString() . PHP_EOL;
}

$tryFromFloatResult3 = BoolStandard::tryFromFloat(2.5);
if (!($tryFromFloatResult3 instanceof Undefined)) {
    echo 'tryFromFloat(2.5): ' . $tryFromFloatResult3->toString() . PHP_EOL;
} else {
    echo 'tryFromFloat(2.5): undefined (invalid float for boolean)' . PHP_EOL;
}

// Also demonstrate with other boolean classes
echo '--- Additional demonstrations ---' . PHP_EOL;
echo 'TrueStandard toBool(): ' . (TrueStandard::fromBool(true)->toBool() ? 'true' : 'false') . PHP_EOL;
echo 'TrueStandard toFloat(): ' . ((string) TrueStandard::fromBool(true)->toFloat()) . PHP_EOL;
echo 'TrueStandard toInt(): ' . TrueStandard::fromBool(true)->toInt() . PHP_EOL;

echo 'FalseStandard toBool(): ' . (FalseStandard::fromBool(false)->toBool() ? 'true' : 'false') . PHP_EOL;
echo 'FalseStandard toFloat(): ' . ((string) FalseStandard::fromBool(false)->toFloat()) . PHP_EOL;
echo 'FalseStandard toInt(): ' . FalseStandard::fromBool(false)->toInt() . PHP_EOL;

// More tryFromBool examples with different classes
$binaryFromBool = Binary::tryFromBool(true);
if (!($binaryFromBool instanceof Undefined)) {
    echo 'Binary::tryFromBool(true): ' . $binaryFromBool->toString() . PHP_EOL;
}

$toggleFromBool = Toggle::tryFromBool(false);
if (!($toggleFromBool instanceof Undefined)) {
    echo 'Toggle::tryFromBool(false): ' . $toggleFromBool->toString() . PHP_EOL;
}

/**
 * Exercise BoolTypeInterface::value() for Psalm.
 */
function testBool(BoolTypeInterface $b): bool
{
    return $b->value();
}

/**
 * Additional test functions to demonstrate interface method usage.
 */
function testToBool(BoolTypeInterface $b): bool
{
    return $b->toBool();
}

function testToFloat(BoolTypeInterface $b): float
{
    return $b->toFloat();
}

function testToInt(BoolTypeInterface $b): int
{
    return $b->toInt();
}

// Call the test functions to ensure they're used
$testBool = BoolStandard::fromBool(true);
testToBool($testBool);
testToFloat($testBool);
testToInt($testBool);
