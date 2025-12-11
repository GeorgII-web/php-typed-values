<?php

namespace PhpTypedValues\Usage\Composite;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Usage\Example\AnyType;
use PhpTypedValues\Usage\Example\NullableType;
use PhpTypedValues\Usage\Example\StrictType;

/**
 * COMPOSITE.
 */
echo PHP_EOL . '> COMPOSITE' . PHP_EOL;

$test = StrictType::fromScalars(1, 'Foobar', 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = AnyType::fromScalars(1, 'Foobar', 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;

$test = NullableType::fromScalars(1, 'Foobar', 170);

echo $test->getId()->toString() . PHP_EOL;
echo $test->getFirstName()->toString() . PHP_EOL;
echo $test->getHeight()->toString() . PHP_EOL;
