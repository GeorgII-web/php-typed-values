<?php

declare(strict_types=1);

use PhpTypedValues\Integer\IntegerStandard;

/**
 * Compute percentage difference: (a / b - 1) * 100.
 */
function pctDiff(float $a, float $b): float
{
    if ($b === 0.0) {
        return 0.0;
    }

    return ($a / $b - 1.0) * 100.0;
}

/** @return array{time_ms: float, mem_delta_mb: float} */
function measureInts(int $n): array
{
    gc_collect_cycles();
    $baseUsage = memory_get_usage(true);

    $ints = [];
    $ints[$n - 1] = 0;

    $t0 = hrtime(true);
    for ($i = 0; $i < $n; ++$i) {
        $ints[$i] = $i;
    }
    $timeMs = (hrtime(true) - $t0) / 1_000_000;

    $memDeltaMb = (memory_get_usage(true) - $baseUsage) / (1024 * 1024);

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
    gc_collect_cycles();
    $baseUsage = memory_get_usage(true);

    $objs = [];
    $objs[$n - 1] = null;

    $t0 = hrtime(true);
    for ($i = 0; $i < $n; ++$i) {
        $objs[$i] = new IntegerStandard($i);
    }
    $timeMs = (hrtime(true) - $t0) / 1_000_000;

    $memDeltaMb = (memory_get_usage(true) - $baseUsage) / (1024 * 1024);

    unset($objs);
    gc_collect_cycles();

    return [
        'time_ms' => $timeMs,
        'mem_delta_mb' => $memDeltaMb,
    ];
}

describe('Performance', function () {
    it('checks object and scalar performance diff', function (): void {
        $n = 1_000_000; // Reduced N for CI stability but still significant

        $objects = measureObjects($n);
        $ints = measureInts($n);

        $timeDiff = pctDiff($objects['time_ms'], $ints['time_ms']);
        $memDiff = pctDiff($objects['mem_delta_mb'], $ints['mem_delta_mb']);

        expect($timeDiff)->toBeLessThanOrEqual(600.0, "Time performance difference ({$timeDiff}%) exceeds 600%")
            ->and($memDiff)->toBeLessThanOrEqual(600.0, "Memory performance difference ({$memDiff}%) exceeds 600%");
    });
});
