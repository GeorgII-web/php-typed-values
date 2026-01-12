<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\ArrayType\ArrayEmptyAbstract;
use PhpTypedValues\ArrayType\ArrayNonEmptyAbstract;
use PhpTypedValues\ArrayType\ArrayOfObjectsAbstract;
use PhpTypedValues\ArrayType\ArrayUndefinedAbstract;
use PhpTypedValues\Bool\Alias\BooleanType;
use PhpTypedValues\Exception\Array\ArrayTypeException;
use PhpTypedValues\Exception\Array\ArrayUndefinedTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Integer\IntegerStandard;
use PhpTypedValues\String\Alias\NonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Usage\Example\OptionalFail;

/**
 * Array.
 */
echo PHP_EOL . '> ARRAY' . PHP_EOL;

try {
    throw new ArrayTypeException('Array type exception occurred');
} catch (ArrayTypeException $exception) {
    // suppress
}

// Defined items
$collection = ArrayOfObjectsAbstract::fromArray(
    [
        IntegerNonNegative::fromInt(1), // Primitive
        OptionalFail::fromScalars(1, 'Foobar', 170), // value object
    ],
);
echo json_encode($collection->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;
echo $collection->count() . PHP_EOL;
echo BooleanType::fromBool($collection->hasUndefined())->toString() . PHP_EOL;
echo BooleanType::fromBool($collection->isEmpty())->toString() . PHP_EOL;
echo BooleanType::fromBool($collection->isUndefined())->toString() . PHP_EOL;

foreach ($collection->value() as $item) {
    if (!$item instanceof Undefined) {
        echo json_encode($item->jsonSerialize(), JSON_THROW_ON_ERROR) . PHP_EOL;
    } else {
        echo 'Undefined' . PHP_EOL;
    }
}

$collection = ArrayOfObjectsAbstract::tryFromArray([1, 2, 3]);
echo $collection->isUndefined() ? 'Undefined array' . PHP_EOL : 'ERROR' . PHP_EOL;

echo ArrayUndefinedAbstract::create()->isUndefined() ? 'Undefined array' . PHP_EOL : 'ERROR' . PHP_EOL;

echo ArrayEmptyAbstract::fromArray([])->isTypeOf(ArrayEmptyAbstract::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;
echo ArrayNonEmptyAbstract::fromArray([1])->isTypeOf(ArrayNonEmptyAbstract::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;
echo ArrayOfObjectsAbstract::fromArray([IntegerStandard::fromString('1')])->isTypeOf(ArrayOfObjectsAbstract::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;
echo ArrayUndefinedAbstract::create()->isTypeOf(ArrayUndefinedAbstract::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;

try {
    ArrayUndefinedAbstract::create()->getDefinedItems();
} catch (ArrayUndefinedTypeException $exception) {
    // suppress
}

try {
    ArrayUndefinedAbstract::create()->toInt();
} catch (ArrayUndefinedTypeException $exception) {
    // suppress
}

try {
    ArrayUndefinedAbstract::create()->toFloat();
} catch (ArrayUndefinedTypeException $exception) {
    // suppress
}

echo (ArrayOfObjectsAbstract::tryFromArray(
    [1, 2],
    ArrayOfObjectsAbstract::tryFromArray([NonEmpty::fromString('One typed item in array as fallback')])
)->isUndefined() ? 'Error' : 'ArrayOfObjects fallback: yes') . PHP_EOL;
