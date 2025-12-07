<?php

declare(strict_types=1);

require_once \dirname(__DIR__, 2) . '/vendor/autoload.php';

use PhpTypedValues\Integer\IntegerStandard;

/**
 * Simple console performance comparison between:
 *  - array<int>
 *  - array<PhpTypedValues\\Integer\\IntegerStandard>
 */
exit(main($argv));

function main(array $argv): int
{
    $defaultN = 1_000_000;
    $n = parseN($argv, $defaultN);

    echo \PHP_EOL;
    echo '---------------------------------------------------------' . \PHP_EOL;

    echo 'PHP: ' . \PHP_VERSION . ' Memory limit: ' . ((string) \ini_get('memory_limit')) . ' Dataset N: ' . number_format($n) . \PHP_EOL;

    // Ensure a clean baseline
    gc_collect_cycles();

    $objects = measureObjects($n);
    $ints = measureInts($n);

    echo \PHP_EOL;
    printf(
        "%-10s | time: %9.3f ms | mem: %7.2f MB\n",
        'ints',
        $ints['time_ms'],
        $ints['mem_delta_mb'],
    );
    printf(
        "%-10s | time: %9.3f ms | mem: %7.2f MB\n",
        'objects',
        $objects['time_ms'],
        $objects['mem_delta_mb'],
    );

    // Percent difference of objects vs ints (how much more/less objects use than ints)
    $timePct = formatPct(pctDiff($objects['time_ms'], $ints['time_ms']));
    $memPct = formatPct(pctDiff($objects['mem_delta_mb'], $ints['mem_delta_mb']));

    printf(
        "%-10s | time: %9s    | mem: %7s\n",
        'diff %',
        $timePct,
        $memPct,
    );

    return 0;
}

function parseN(array $argv, int $fallback): int
{
    // Allow: php script.php 1000000
    $arg = $argv[1] ?? null;
    if ($arg === null) {
        return $fallback;
    }

    if (!is_numeric($arg)) {
        return $fallback;
    }

    $n = (int) $arg;

    return $n > 0 ? $n : $fallback;
}

// Helpers *************************************************************************************************************

/** @return array{time_ms: float, mem_delta_mb: float} */
function measureInts(int $n): array
{
    $baseUsage = memory_get_usage(true);

    $ints = [];
    $ints[$n - 1] = 0; // pre-allocate last index (sparse) to reduce reallocs

    $t0 = hrtime(true);
    for ($i = 0; $i < $n; ++$i) {
        $ints[$i] = $i;
    }
    $buildElapsed = (hrtime(true) - $t0) / 1_000_000; // ms

    // Calculate memory delta
    $memDeltaMb = (memory_get_usage(true) - $baseUsage) / (1024 * 1024);

    // Keep the larger timing as total for reporting granularity
    $timeMs = $buildElapsed; // report build time separately below

    // Cleanup
    unset($ints);
    gc_collect_cycles();

    return [
        'time_ms' => $timeMs,
        'mem_delta_mb' => $memDeltaMb,
    ];
}

/** @return array{time_ms: float, mem_delta_mb: float} */
function measureObjects(int $n): array
{
    $baseUsage = memory_get_usage(true);

    $objs = [];
    $objs[$n - 1] = null; // pre-allocate (sparse)

    $t0 = hrtime(true);
    for ($i = 0; $i < $n; ++$i) {
        $objs[$i] = new IntegerStandard($i);
    }
    $buildElapsed = (hrtime(true) - $t0) / 1_000_000; // ms

    $memDeltaMb = (memory_get_usage(true) - $baseUsage) / (1024 * 1024);

    unset($objs);
    gc_collect_cycles();

    return [
        'time_ms' => $buildElapsed,
        'mem_delta_mb' => $memDeltaMb,
    ];
}

// Percentage helpers **************************************************************************************************

/**
 * Compute percentage difference: (a / b - 1) * 100. Returns null when baseline is zero or not finite.
 */
function pctDiff(float $a, float $b): ?float
{
    if ($b === 0.0 || !is_finite($a) || !is_finite($b)) {
        return null;
    }

    return ($a / $b - 1.0) * 100.0;
}

/**
 * Format percentage with sign and 1 decimal place. Returns '   n/a' when input is null.
 */
function formatPct(?float $pct): string
{
    if ($pct === null) {
        return '   n/a';
    }

    return \sprintf('%+6.1f%%', $pct);
}
