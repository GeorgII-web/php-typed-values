<?php

require_once 'vendor/autoload.php';

use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;
use PhpTypedValues\Undefined\Alias\NotFound;
use PhpTypedValues\Undefined\Alias\NotSet;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Undefined\Alias\Unknown;
use PhpTypedValues\Undefined\UndefinedStandard;

// Undefined
try {
    UndefinedStandard::create()->toString();
} catch (UndefinedTypeException $e) {
    // suppress
}
try {
    UndefinedStandard::create()->toInt();
} catch (UndefinedTypeException $e) {
    // suppress
}
try {
    UndefinedStandard::create()->toFloat();
} catch (UndefinedTypeException $e) {
    // suppress
}
try {
    NotExist::create()->value();
} catch (UndefinedTypeException $e) {
    // suppress
}
NotFound::create();
NotSet::create();
Undefined::create();
Unknown::create();
