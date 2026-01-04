<?php

declare(strict_types=1);

namespace PhpTypedValues\Integer\Alias\Specific;

use PhpTypedValues\Integer\Specific\IntegerWeekDay;

/**
 * WeekDay (Alias for weekday integer).
 *
 * Provides the same behavior as IntegerWeekDay while exposing a concise
 * name suitable for APIs that prefer "WeekDay".
 *
 * Example
 *  - $v = WeekDay::fromInt(1);
 *    $v->value(); // 1
 *  - $v = WeekDay::fromString('7');
 *    (string) $v; // "7"
 *
 * @psalm-immutable
 */
final readonly class WeekDay extends IntegerWeekDay
{
}
