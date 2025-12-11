<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Exception\ArrayTypeException;

/**
 * Array.
 */
echo PHP_EOL . '> ARRAY' . PHP_EOL;

try {
    throw new ArrayTypeException('Array type exception occurred');
} catch (ArrayTypeException $exception) {
    // suppress
}
