<?php

use PhpTypedValues\Abstract\Bool\BoolTypeInterface;
use PhpTypedValues\Bool\Alias\Boolean;
use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Exception\UndefinedTypeException;

/**
 * Boolean.
 */
$undefinedType1 = BoolStandard::tryFromInt(2);
$undefinedType2 = BoolStandard::tryFromString('test');
try {
    echo $undefinedType1->value();
    echo $undefinedType2->value();
} catch (UndefinedTypeException $e) {
    // suppress
}
echo BoolStandard::fromString('true')->toString() . \PHP_EOL;
echo BoolStandard::fromInt(1)->toString() . \PHP_EOL;
echo BoolStandard::fromBool(true)->toString() . \PHP_EOL;
echo Boolean::fromBool(Boolean::fromBool(true)->value())->toString() . \PHP_EOL;
// Ensure interface method usage is visible to Psalm
echo (testBool(BoolStandard::fromBool(true)) ? 'true' : 'false') . \PHP_EOL;

/**
 * Exercise BoolTypeInterface::value() for Psalm.
 */
function testBool(BoolTypeInterface $b): bool
{
    return $b->value();
}
