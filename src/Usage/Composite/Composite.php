<?php

namespace PhpTypedValues\Usage\Composite;

require_once 'vendor/autoload.php';

use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

use PhpTypedValues\Usage\Example\ArrayOfStrings;
use PhpTypedValues\Usage\Example\EarlyFail;
use PhpTypedValues\Usage\Example\LateFail;
use PhpTypedValues\Usage\Example\OptionalFail;

/**
 * COMPOSITE.
 */
echo PHP_EOL . '> COMPOSITE' . PHP_EOL;

$test = EarlyFail::fromScalars(1, 'Foobar', 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = LateFail::fromScalars(1, 'Foobar', 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = OptionalFail::fromScalars(1, 'Foobar', 170);
echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;
echo json_encode($test, JSON_THROW_ON_ERROR) . PHP_EOL;

$test = ArrayOfStrings::tryFromArray(['string', 'not-empty', '1', 2, true]);
echo json_encode($test, JSON_THROW_ON_ERROR) . PHP_EOL;
echo json_encode($test->value(), JSON_THROW_ON_ERROR) . PHP_EOL;
