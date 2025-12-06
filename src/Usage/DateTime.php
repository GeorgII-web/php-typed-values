<?php

require_once 'vendor/autoload.php';

use PhpTypedValues\DateTime\DateTimeAtom;
use PhpTypedValues\DateTime\DateTimeRFC3339;
use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;

/**
 * DateTime.
 */
echo DateTimeAtom::getFormat() . \PHP_EOL;

$dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00')->value();
echo DateTimeAtom::fromDateTime($dt)->toString() . \PHP_EOL;

$dt = DateTimeRFC3339::fromString('2025-01-02T03:04:05+00:00')->value();
echo DateTimeRFC3339::fromDateTime($dt)->toString() . \PHP_EOL;

// Timestamp
$tsVo = TimestampSeconds::fromString('1735787045');
echo TimestampSeconds::fromDateTime($tsVo->value())->toString() . \PHP_EOL;

$tsVo = TimestampMilliseconds::fromString('1735787045123');
echo TimestampMilliseconds::fromDateTime($tsVo->value())->toString() . \PHP_EOL;
