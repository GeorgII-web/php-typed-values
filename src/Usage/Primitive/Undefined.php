<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const INF;
use const NAN;
use const PHP_EOL;
use const PHP_INT_MAX;

use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
use PhpTypedValues\Undefined\Alias\NotExist;
use PhpTypedValues\Undefined\Alias\NotFound;
use PhpTypedValues\Undefined\Alias\NotSet;
use PhpTypedValues\Undefined\Alias\Undefined;
use PhpTypedValues\Undefined\Alias\Unknown;
use PhpTypedValues\Undefined\UndefinedStandard;

use function sprintf;

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

echo '--------------------------------------------------------------------------------' . PHP_EOL;

$floats = [
    // Zero variants
    '0' => 0,
    '0.0' => 0.0,
    '-0.0' => -0.0,
    '+0.0' => +0.0,

    // Simple values
    '1.0' => 1.0,
    '-1.0' => -1.0,
    '0.1' => 0.1,
    '0.10000000000000001' => 0.10000000000000001,
    '0.10000000000000002' => 0.10000000000000002,
    '0.10000000000000012' => 0.10000000000000012,
    '-0.1' => -0.1,
    '0.2' => 0.2,
    '0.3' => 0.3,

    // Floating-point precision traps
    '0.1+0.2' => 0.1 + 0.2,        // 0.30000000000000004
    '1/3' => 1.0 / 3.0,
    '2/3' => 2.0 / 3.0,
    '10/3' => 10.0 / 3.0,

    // Large numbers
    '1e10' => 1e10,
    '-1e10' => -1e10,
    '1e308' => 1e308,            // near PHP float max
    '-1e308' => -1e308,

    // Small numbers
    '1e-10' => 1e-10,
    '-1e-10' => -1e-10,
    '1e-308' => 1e-308,            // near min positive normal
    '-1e-308' => -1e-308,

    // Subnormal numbers
    '5e-324' => 5e-324,            // smallest positive float
    '-5e-324' => -5e-324,

    // Rounding boundaries
    '1.99999999999999' => 1.99999999999999,
    '2.00000000000001' => 2.00000000000001,

    // Fractions that don’t convert cleanly
    '0.7' => 0.7,
    '0.17' => 0.17,
    '0.57' => 0.57,
    '0.99' => 0.99,

    '001.5' => 001.5,

    // Repeating decimal approximations
    '0.3333333333333333' => 0.3333333333333333,
    '0.6666666666666666' => 0.6666666666666666,

    // Integers beyond safe precision
    '2^53-1' => 9007199254740991.0, // max safe int
    '2^53' => 9007199254740992.0, // precision loss
    '-2^53' => -9007199254740992.0,

    // Special values
    'INF' => INF,
    '-INF' => -INF,
    'NAN' => NAN,
    'PHP_INT_MAX' => PHP_INT_MAX,
];

function floatToString(float $value): string
{
    // Convert to string without scientific notation
    $strValue = sprintf('%.17f', $value);

    // Trim trailing zeros but keep at least one decimal
    $strValue = rtrim($strValue, '0');
    if (str_ends_with($strValue, '.')) {
        $strValue .= '0';
    }

    //    // Ensure leading zero
    //    if ($strValue[0] === '.') {
    //        $strValue = '0' . $strValue;
    //    }
    //    if ($strValue[0] === '-' && $strValue[1] === '.') {
    //        $strValue = '-0' . substr($strValue, 1);
    //    }

    if ($value !== (float) $strValue) {
        throw new FloatTypeException(sprintf('Float "%s" has no valid strict string value', $value));
    }

    /**
     * @var non-empty-string
     */
    return $strValue;
}

printf(
    "%-25s | %-25s | %-25s | %-25s\n",
    'Source value',
    'As stored (%.17g)',
    'Cast to string',
    'Class string'
);
echo str_repeat('-', 82) . PHP_EOL;

foreach ($floats as $key => $f) {
    $source = $key; // var_export($f, true);

    // Best practical view of internal float value
    $stored = is_finite($f)
        ? sprintf('%.17g', $f)
        : ($f === INF ? 'INF' : ($f === -INF ? '-INF' : 'NAN'));

    $string = (string) $f;

    try {
        $stringClass = floatToString($f);
    } catch (FloatTypeException $e) {
        $stringClass = 'Invalid *****************';
    }

    printf(
        "%-25s | %-25s | %-25s | %-25s\n",
        $source,
        $stored,
        $string,
        $stringClass
    );
}
