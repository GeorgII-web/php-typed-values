<?php

namespace PhpTypedValues\Usage\Composite;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\Usage\Example\EarlyFail;
use PhpTypedValues\Usage\Example\LateFail;
use PhpTypedValues\Usage\Example\OptionalFail;
use PhpTypedValues\Usage\Example\WithArrays;

/**
 * COMPOSITE.
 */
echo PHP_EOL . '> COMPOSITE' . PHP_EOL;

$test = EarlyFail::fromScalars(id: 1, firstName: 'Foobar', height: 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = LateFail::fromScalars(id: 1, firstName: 'Foobar', height: 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = OptionalFail::fromScalars(id: 1, firstName: 'Foobar', height: 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;
echo json_encode($test, JSON_THROW_ON_ERROR) . PHP_EOL;

$test = WithArrays::fromScalars(
    id: 1,
    firstName: 'Foobar',
    height: 170,
    nickNames: ['User1', 'Admin5'],
);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;
$nickNames = $test->getNickNames();
echo ($nickNames->isUndefined() ? 'true' : 'false')
    . ' ' . ($nickNames->isEmpty() ? 'true' : 'false')
    . ' ' . ($nickNames->hasUndefined() ? 'true' : 'false')
    . ' ' . $nickNames->count() . PHP_EOL;
echo json_encode($nickNames->toArray(), JSON_THROW_ON_ERROR) . PHP_EOL;
echo json_encode($test, JSON_THROW_ON_ERROR) . PHP_EOL;
