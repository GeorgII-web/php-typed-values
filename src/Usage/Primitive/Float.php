<?php

namespace PhpTypedValues\Usage\Primitive;

require_once 'vendor/autoload.php';

use const INF;
use const M_PI;
use const NAN;
use const PHP_EOL;

use PhpTypedValues\Float\Alias\DoubleType;
use PhpTypedValues\Float\Alias\FloatType;
use PhpTypedValues\Float\Alias\NonNegative;
use PhpTypedValues\Float\Alias\Positive;
use PhpTypedValues\Float\FloatNonNegative;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Float\FloatStandard;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;
use function strlen;

/**
 * Float.
 */
echo PHP_EOL . '> FLOAT' . PHP_EOL;

testFloat(FloatStandard::fromFloat(3.14)->value());

echo (string) FloatStandard::tryFromInt(1)->toFloat() . PHP_EOL;
echo FloatStandard::tryFromBool(true)->toBool() ? 'true' . PHP_EOL : 'false' . PHP_EOL;
echo FloatStandard::fromString('1.0')->toString() . PHP_EOL;
echo (string) FloatStandard::fromString('2.0')->toInt() . PHP_EOL;
echo FloatStandard::tryFromFloat(2.71828)->toString() . PHP_EOL;
echo FloatNonNegative::tryFromMixed('2.0')->toString() . PHP_EOL;
echo FloatNonNegative::tryFromFloat(2.71828)->toString() . PHP_EOL;
echo NonNegative::fromString('2.0')->toString() . PHP_EOL;
echo FloatType::fromString('2.0')->toString() . PHP_EOL;
echo DoubleType::fromString('2.0')->toString() . PHP_EOL;
echo DoubleType::fromString('2.0')->toString() . PHP_EOL;
echo Positive::fromString('2.0')->toString() . PHP_EOL;
echo FloatStandard::tryFromMixed('2.0')->toString() . PHP_EOL;

// PositiveFloat usage
testPositiveFloat(FloatNonNegative::fromFloat(0.5)->value());
echo FloatNonNegative::fromString('0.123456781')->toString() . PHP_EOL;

// try* usages to satisfy Psalm (ensure both success and failure branches are referenced)
$ts = FloatStandard::tryFromString('1.23');
if (!($ts instanceof Undefined)) {
    echo $ts->toString() . PHP_EOL;
}

$ti = FloatPositive::tryFromFloat(2.2);
if (!($ti instanceof Undefined)) {
    echo $ti->toString() . PHP_EOL;
}

$tn = FloatNonNegative::tryFromString('-1'); // will likely be Undefined
if (!($tn instanceof Undefined)) {
    echo $tn->toString() . PHP_EOL;
}

echo FloatStandard::fromString('1.0')->isTypeOf(FloatStandard::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;

/**
 * Artificial functions.
 */
function testFloat(float $f): float
{
    return $f;
}

function testPositiveFloat(float $f): float
{
    return $f;
}

echo ' -------------------------------------------------------------- ' . PHP_EOL;

function stringToFloat(string $s): ?float
{
    // reject any whitespace
    if ($s !== trim($s)) {
        return null;
    }

    // strict DECIMAL float literal only (no exponent)
    // rules:
    // - optional leading "-"
    // - NO leading "+"
    // - must contain a decimal point
    // - no trailing dot, no leading dot
    // - no leading zeroes like 01.0
    if (!preg_match(
        '/^-?(?:0|[1-9]\d*)\.\d+$/',
        $s
    )) {
        return null;
    }

    $f = (float) $s;

    // reject NaN / INF explicitly
    if (is_nan($f) || is_infinite($f)) {
        return null;
    }

    return $f;
}

$tests = [
    // ─────────────
    // INVALID: integers (no decimal / no exponent)
    // ─────────────
    '0' => null,
    '1' => null,
    '-1' => null,
    '+1' => null,

    // ─────────────
    // VALID: minimal floats
    // ─────────────
    '0.0' => 0.0,
    '-0.0' => -0.0,
    '1.0' => 1.0,
    '-1.0' => -1.0,

    // ─────────────
    // INVALID: malformed decimals
    // ─────────────
    '+0.0' => null,
    '.' => null,
    '.0' => null,
    '0.' => null,
    '+.0' => null,
    '1.' => null,
    '00.0' => null,   // formatting strictness
    '01.0' => null,

    // ─────────────
    // VALID: normal decimals (exact)
    // ─────────────
    '2.0' => 2.0,
    '10.5' => 10.5,
    '-10.5' => -10.5,
    '0.25' => 0.25,
    '0.5' => 0.5,
    '0.125' => 0.125,
    '0.0625' => 0.0625,

    // ─────────────
    // VALID: lossy decimals
    // ─────────────
    '0.1' => 0.1,
    '0.2' => 0.2,
    '0.3' => 0.3,
    '0.15' => 0.15,
    '0.3333333333333333' => 0.3333333333333333,

    // ─────────────
    // VALID: edge exact fractions
    // ─────────────
    '0.75' => 0.75,
    '1.5' => 1.5,
    '3.75' => 3.75,

    // ─────────────
    // INVALID: special exponential form
    // ─────────────
    '1e10' => null,
    '1.0e+10' => null,
    '1e16' => null,
    '5e-324' => null, // smallest positive subnormal
    '1e-323' => null,
    '1e-1' => null,    // 0.1
    '3e-1' => null,    // 0.3
    '1.1e1' => null,
    '1e0' => null,
    '1.0e0' => null,
    '5e-1' => null,
    '125e-3' => null,
    '1e1' => null,
    '1e2' => null,
    '9.007199254740992e15' => null, // 2^53
    '4.503599627370496e15' => null, // 2^52
    '2.2250738585072014e-308' => null, // min normal
    '1.7976931348623157e308' => null,  // max finite

    // ─────────────
    // VALID: beyond integer precision (rounded by IEEE-754)
    // ─────────────
    '9007199254740993.0' => 9007199254740992.0, // rounded to 2^53

    // ─────────────
    // VALID: beyond float precision
    // ─────────────
    '179769313486231000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000.0' => 179769313486231000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000.0,

    // ─────────────
    // INVALID: special values
    // ─────────────
    'NaN' => null,
    'INF' => null,
    '-INF' => null,
    'pi()' => null,

    // ─────────────
    // INVALID: whitespace / junk
    // ─────────────
    ' 1.0' => null,
    '1.0 ' => null,
    "1.0\n" => null,
    '1.0foo' => null,
];

// ANSI colors
$GREEN = "\033[32m";
$RED = "\033[31m";
$RESET = "\033[0m";

/**
 * Format float for clean human-readable display
 * Removes trailing zeros and decimal point.
 */
function formatHumanFloat(?float $value): string
{
    if ($value === null) {
        return 'null';
    }

    $string = var_export($value, true);

    // If it's scientific notation, keep it as-is
    if (str_contains($string, 'E')) {
        return $string;
    }

    // Remove trailing zeros and decimal point
    return rtrim(rtrim($string, '0'), '.');
}

// header
printf(
    "%-28s | %-22s | %-22s | %-22s | RESULT\n",
    'SRC',
    'EXP.MEMORY',
    'EXP.HUMAN',
    'GOT'
);
echo str_repeat('-', 115) . "\n";

foreach ($tests as $src => $expected) {
    $got = stringToFloat((string) $src);

    // EXPECTED columns
    $expMemory = ($expected === null)
        ? 'null'
        : sprintf('%.17g', $expected);

    $expHuman = formatHumanFloat($expected);

    // GOT column - now in human format
    $gotHuman = formatHumanFloat($got);

    // For comparison, we still need the full precision string
    $gotMemory = ($got === null)
        ? 'null'
        : sprintf('%.17g', $got);

    // Compare using full precision strings
    $pass = ($expected === null)
        ? ($got === null)
        : ($got !== null && $gotMemory === $expMemory);

    $color = $pass ? $GREEN : $RED;
    $result = $pass ? 'PASS' : 'FAIL';

    printf(
        "%-28s | %-22s | %-22s | %-22s | %s%s%s\n",
        "'{(string) {$src}}'",
        $expMemory,
        $expHuman,
        $gotHuman,  // Now showing human format
        $color,
        $result,
        $RESET
    );
}

echo ' -------------------------------------------------------------- ' . PHP_EOL;

function floatToString(float $f): ?string
{
    // handle special values
    if (is_nan($f) || is_infinite($f)) {
        return null;
    }

    // canonical zero
    if ($f == 0.0) {
        return '0.0';
    }

    // preserve negative sign
    $neg = ($f < 0);
    $abs = abs($f);

    // try %.17g first
    $s = sprintf('%.17g', $abs);

    // force no exponent: if 'e' found, expand manually
    if (stripos($s, 'e') !== false) {
        // separate mantissa and exponent
        if (!preg_match('/([0-9.]+)[eE]([+-]?\d+)/', $s, $m)) {
            return null; // shouldn't happen
        }
        $mant = $m[1];
        $exp = (int) $m[2];

        // split mantissa into integer and fraction
        $parts = explode('.', $mant, 2);
        $intPart = $parts[0];
        $fracPart = $parts[1] ?? '';

        if ($exp >= 0) {
            // shift decimal to the right
            $fracLen = strlen($fracPart);
            if ($exp >= $fracLen) {
                $fracPart .= str_repeat('0', $exp - $fracLen);
                $s = $intPart . $fracPart . '.0';
            } else {
                $s = $intPart . substr($fracPart, 0, $exp) . '.' . substr($fracPart, $exp);
            }
        } else {
            // shift decimal to the left
            $s = '0.' . str_repeat('0', -$exp - strlen($intPart)) . $intPart . $fracPart;
        }
    } else {
        // ensure .0 for integer-looking numbers
        if (!str_contains($s, '.')) {
            $s .= '.0';
        }
    }

    return $neg ? "-{$s}" : $s;
}

$tests2 = [
    // ─────────────
    // ZEROES
    // ─────────────
    ['src' => 0.0,    'expected' => '0.0'],
    ['src' => -0.0,   'expected' => '0.0'],  // canonical zero
    ['src' => +0.0,   'expected' => '0.0'],

    // ─────────────
    // SIMPLE INTEGERS
    // ─────────────
    ['src' => 1.0,    'expected' => '1.0'],
    ['src' => -1.0,   'expected' => '-1.0'],
    ['src' => +1.0,   'expected' => '1.0'],

    ['src' => 2.0,    'expected' => '2.0'],
    ['src' => 10.0,   'expected' => '10.0'],

    // ─────────────
    // NORMAL DECIMALS
    // ─────────────
    ['src' => 0.5,    'expected' => '0.5'],
    ['src' => 0.25,   'expected' => '0.25'],
    ['src' => 0.75,   'expected' => '0.75'],
    ['src' => 1.5,    'expected' => '1.5'],
    ['src' => 3.75,   'expected' => '3.75'],
    ['src' => 0.1,    'expected' => '0.1'],
    ['src' => 0.2,    'expected' => '0.20000000000000001'],
    ['src' => 0.3,    'expected' => '0.29999999999999999'],
    ['src' => 0.15,   'expected' => '0.14999999999999999'],
    ['src' => 0.3333333333333333, 'expected' => '0.33333333333333331'],

    // ─────────────
    // LONG FLOATS (originally exponential)
    // ─────────────
    ['src' => 1e10,     'expected' => '10000000000.0'],
    ['src' => 1.0e10,   'expected' => '10000000000.0'],
    ['src' => 1e16,     'expected' => '10000000000000000.0'],
    ['src' => 5e-324,   'expected' => '0.0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000049406564584124654'],
    ['src' => 1e-323,   'expected' => '0.0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000098813129168249309'],
    ['src' => 0.11111111167890123456789111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111234567890,   'expected' => '0.11111111167890124'],

    // ─────────────
    // FRACTIONS / IRRATIONALS
    // ─────────────
    ['src' => 2 / 3,    'expected' => '0.66666666666666663'],
    ['src' => M_PI,   'expected' => '3.1415926535897931'],

    // ─────────────
    // INVALID SPECIALS
    // ─────────────
    ['src' => INF,    'expected' => null],
    ['src' => -INF,   'expected' => null],
    ['src' => NAN,    'expected' => null],
];

// Header
printf(
    "%-28s | %-22s | %-22s | RESULT\n",
    'SRC',
    'EXPECTED',
    'GOT'
);
echo str_repeat('-', 95) . "\n";

// Loop over dataset (new structure)
foreach ($tests2 as $row) {
    $src = $row['src'];
    $expected = $row['expected'];

    $got = floatToString($src);

    // canonicalize null as string
    $expStr = ($expected === null) ? 'null' : $expected;
    $gotStr = ($got === null) ? 'null' : $got;

    // strict equality
    $pass = $expStr === $gotStr;

    $color = $pass ? $GREEN : $RED;
    $result = $pass ? 'PASS' : 'FAIL';

    printf(
        "%-28s | %-22s | %-22s | %s%s%s\n",
        var_export($src, true),
        $expStr,
        $gotStr,
        $color,
        $result,
        $RESET
    );
}
