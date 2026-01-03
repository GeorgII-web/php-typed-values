<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Base\Primitive\Bool\BoolTypeInterface;
use PhpTypedValues\Bool\Alias\Binary;
use PhpTypedValues\Bool\Alias\Boolean;
use PhpTypedValues\Bool\Alias\Flag;
use PhpTypedValues\Bool\Alias\Logical;
use PhpTypedValues\Bool\Alias\Toggle;
use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Bool\FalseStandard;
use PhpTypedValues\Bool\TrueStandard;
use PhpTypedValues\Exception\UndefinedTypeException;
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
echo Boolean::fromBool(Boolean::fromBool(true)->value())->toString() . PHP_EOL;
// Ensure interface method usage is visible to Psalm
echo (testBool(BoolStandard::fromBool(true)) ? 'true' : 'false') . PHP_EOL;
echo TrueStandard::tryFromMixed('yes')->toString() . PHP_EOL;
echo Binary::tryFromMixed('yes')->toString() . PHP_EOL;
echo Flag::tryFromMixed('yes')->toString() . PHP_EOL;
echo Logical::tryFromMixed('yes')->toString() . PHP_EOL;
echo Toggle::tryFromMixed('yes')->toString() . PHP_EOL;

echo Toggle::fromString('1')->isTypeOf(Toggle::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;

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

/**
 * Exercise BoolTypeInterface::value() for Psalm.
 */
function testBool(BoolTypeInterface $b): bool
{
    return $b->value();
}
