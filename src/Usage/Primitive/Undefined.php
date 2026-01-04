<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const PHP_EOL;

use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;
use PhpTypedValues\Undefined\Alias\NotFound;
use PhpTypedValues\Undefined\Alias\NotSet;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Undefined\Alias\Unknown;
use PhpTypedValues\Undefined\UndefinedStandard;

// Undefined
echo PHP_EOL . '> UNDEFINED' . PHP_EOL;

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
Unknown::create();

$undefined = Unknown::fromString('hi');
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromString('hi');
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromMixed('hi');
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromBool(true);
try {
    $undefined->toBool();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromFloat(1.1);
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromInt(11);
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Unknown::tryFromArray([]);
try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

$undefined = Undefined::create();
try {
    $undefined->value();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $undefined->toString();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $undefined->toFloat();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $undefined->toInt();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $undefined->toArray();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $undefined->jsonSerialize();
} catch (TypeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

echo Undefined::fromString('no')->isTypeOf(Undefined::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;
