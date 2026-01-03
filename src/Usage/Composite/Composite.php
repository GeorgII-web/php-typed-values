<?php

namespace PhpTypedValues\Usage\Composite;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\ArrayType\ArrayEmpty;
use PhpTypedValues\Usage\Example\EarlyFail;
use PhpTypedValues\Usage\Example\LateFail;
use PhpTypedValues\Usage\Example\OptionalFail;
use PhpTypedValues\Usage\Example\WithArrays;

/**
 * COMPOSITE.
 */
echo PHP_EOL . '> COMPOSITE' . PHP_EOL;

$testEarly = EarlyFail::fromScalars(1, 'Foobar', 170);
echo $testEarly->getId()->toString() . PHP_EOL;
echo $testEarly->getFirstName()->toString() . PHP_EOL;
echo $testEarly->getHeight()->toString() . PHP_EOL;
$testEarly2 = EarlyFail::fromArray(['id' => 1, 'firstName' => 'ss', 'height' => 12.2]);
echo json_encode($testEarly2->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;

$testLate = LateFail::fromScalars(1, 'Foobar', 170);
echo $testLate->getId()->toString() . PHP_EOL;
echo $testLate->getFirstName()->toString() . PHP_EOL;
echo $testLate->getHeight()->toString() . PHP_EOL;

$testOptional = OptionalFail::fromScalars(1, 'Foobar', 170);
echo $testOptional->getId()->toString() . PHP_EOL;
echo $testOptional->getFirstName()->toString() . PHP_EOL;
echo $testOptional->getHeight()->toString() . PHP_EOL;
echo json_encode($testOptional, JSON_THROW_ON_ERROR) . PHP_EOL;

$testArray = WithArrays::fromScalars(
    1,
    'Foobar',
    170,
    ['User1', 'Admin5'],
);
echo $testArray->getId()->toString() . PHP_EOL;
echo $testArray->getFirstName()->toString() . PHP_EOL;
echo $testArray->getHeight()->toString() . PHP_EOL;
$nickNames = $testArray->getNickNames();
echo ($nickNames->isUndefined() ? 'true' : 'false')
    . ' ' . ($nickNames->isEmpty() ? 'true' : 'false')
    . ' ' . ($nickNames->hasUndefined() ? 'true' : 'false')
    . ' ' . $nickNames->count() . PHP_EOL;
echo json_encode($nickNames->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;
echo json_encode($testArray, JSON_THROW_ON_ERROR) . PHP_EOL;

$testEmpty = ArrayEmpty::fromArray([]);
echo json_encode($testEmpty->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;
