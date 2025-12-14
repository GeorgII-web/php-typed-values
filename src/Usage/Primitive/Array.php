<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\Array\ArrayOfObjects;
use PhpTypedValues\Bool\Alias\Boolean;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Usage\Example\OptionalFail;

use function count;

/**
 * Array.
 */
echo PHP_EOL . '> ARRAY' . PHP_EOL;

try {
    throw new ArrayTypeException('Array type exception occurred');
} catch (ArrayTypeException) {
    // suppress
}

// Defined items
$collection = ArrayOfObjects::fromArray(
    [
        IntegerNonNegative::fromInt(1), // Primitive
        OptionalFail::fromScalars(id: 1, firstName: 'Foobar', height: 170), // value object
    ],
);
echo json_encode($collection->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;

// Undefined item
$collection = ArrayOfObjects::tryFromArray(
    [
        IntegerNonNegative::fromInt(1), // Primitive
        OptionalFail::fromScalars(id: 1, firstName: 'Foobar', height: 170), // value object
        1, // scalar > Undefined
    ],
);
echo $collection->count() . PHP_EOL;
echo Boolean::fromBool($collection->hasUndefined())->toString() . PHP_EOL;
echo Boolean::fromBool($collection->isEmpty())->toString() . PHP_EOL;
echo Boolean::fromBool($collection->isUndefined())->toString() . PHP_EOL;

foreach ($collection->value() as $item) {
    if (!$item instanceof Undefined) {
        echo json_encode($item->jsonSerialize(), JSON_THROW_ON_ERROR) . PHP_EOL;
    } else {
        echo 'Undefined' . PHP_EOL;
    }
}

// Demonstrate usage of getDefinedItems() to exclude Undefined values
$defined = $collection->getDefinedItems();
echo 'Defined items count: ' . count($defined) . PHP_EOL;
