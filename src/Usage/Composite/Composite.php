<?php

namespace PhpTypedValues\Usage\Composite;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Usage\Example\AnyType;
use PhpTypedValues\Usage\Example\OptionalType;
use PhpTypedValues\Usage\Example\StrictType;

/**
 * COMPOSITE.
 */
echo PHP_EOL . '> COMPOSITE' . PHP_EOL;

$test = StrictType::fromScalars(id: 1, firstName: 'Foobar', height: 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = AnyType::fromScalars(id: 1, firstName: 'Foobar', height: 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = OptionalType::fromScalars(id: 1, firstName: 'Foobar', height: 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;
