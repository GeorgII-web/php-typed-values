<?php

namespace App\Usage\Primitive;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\Array\ArrayOfObjects;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Integer\IntegerNonNegative;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Usage\Example\OptionalFail;

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
echo $collection->hasUndefined() ? 'true' : 'false' . PHP_EOL;
echo $collection->isEmpty() ? 'true' : 'false' . PHP_EOL;
echo $collection->isUndefined() ? 'true' : 'false' . PHP_EOL;

foreach ($collection->value() as $item) {
    if (!$item instanceof Undefined) {
        echo json_encode($item->jsonSerialize(), JSON_THROW_ON_ERROR) . PHP_EOL;
    } else {
        echo 'Undefined' . PHP_EOL;
    }
}
