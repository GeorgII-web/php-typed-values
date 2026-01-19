<?php

//
// namespace PhpTypedValues\Usage\Primitive;
//
// require_once 'vendor/autoload.php';
//
// use const PHP_EOL;
//
// use PhpTypedValues\DateTime\DateTimeAtom;
// use PhpTypedValues\DateTime\DateTimeRFC3339;
// use PhpTypedValues\DateTime\DateTimeRFC3339Extended;
// use PhpTypedValues\DateTime\DateTimeW3C;
// use PhpTypedValues\DateTime\MariaDb\DateTimeSql;
// use PhpTypedValues\DateTime\Timestamp\TimestampMilliseconds;
// use PhpTypedValues\DateTime\Timestamp\TimestampSeconds;
// use PhpTypedValues\Undefined\Alias\Undefined;
//
// /**
// * DateTime.
// */
// echo PHP_EOL . '> DATETIME' . PHP_EOL;
//
// echo DateTimeAtom::getFormat() . PHP_EOL;
// echo DateTimeRFC3339::getFormat() . PHP_EOL;
// echo DateTimeRFC3339Extended::getFormat() . PHP_EOL;
// echo DateTimeW3C::getFormat() . PHP_EOL;
// echo TimestampSeconds::getFormat() . PHP_EOL;
// echo TimestampMilliseconds::getFormat() . PHP_EOL;
// echo DateTimeSql::getFormat() . PHP_EOL;
// // echo DateTimeAtom::tryFromMixed('2025-01-02T03:04:05+00:00')->toString() . PHP_EOL;
// // echo DateTimeSql::tryFromMixed('2025-01-02 03:04:05')->toString() . PHP_EOL;
// echo DateTimeSql::tryFromMixed('2025-01-02 03:04:05', 'UTC')->isTypeOf(DateTimeSql::class) ? 'Type correct' . PHP_EOL : 'Invalid type' . PHP_EOL;
//
// $dt = DateTimeAtom::fromString('2025-01-02T03:04:05+00:00')->value();
// echo DateTimeAtom::fromDateTime($dt)->toString() . PHP_EOL;
//
// $dt = DateTimeRFC3339::fromString('2025-01-02T03:04:05+00:00')->value();
// echo DateTimeRFC3339::fromDateTime($dt)->toString() . PHP_EOL;
//
// // Timestamp
// $tsVo = TimestampSeconds::fromString('1735787045');
// echo TimestampSeconds::fromInt(1735787045)->toString() . PHP_EOL;
// echo TimestampSeconds::fromDateTime($tsVo->value())->toString() . PHP_EOL;
//
// $tsVo = TimestampMilliseconds::fromString('1735787045123');
// echo TimestampMilliseconds::fromInt(1735787045123)->toString() . PHP_EOL;
// echo TimestampMilliseconds::fromDateTime($tsVo->value())->toString() . PHP_EOL;
//
// // tryFromString usages to satisfy Psalm (ensure both success and failure branches are referenced)
// $a = DateTimeAtom::tryFromString('2025-01-02T03:04:05+00:00');
// if (!($a instanceof Undefined)) {
//    echo $a->toString() . PHP_EOL;
//    echo $a->withTimeZone('Europe/Berlin')->toString() . PHP_EOL;
// }
//
// $r = DateTimeRFC3339::tryFromString('2025-01-02T03:04:05+00:00');
// if (!($r instanceof Undefined)) {
//    echo $r->toString() . PHP_EOL;
//    echo $r->withTimeZone('America/New_York')->toString() . PHP_EOL;
// }
//
// $re = DateTimeRFC3339Extended::tryFromString('2025-01-02T03:04:05.123456+00:00');
// if (!($re instanceof Undefined)) {
//    echo $re->toString() . PHP_EOL;
// }
//
// $w = DateTimeW3C::tryFromString('2025-01-02T03:04:05+00:00');
// if (!($w instanceof Undefined)) {
//    echo $w->toString() . PHP_EOL;
//    echo $w->withTimeZone('UTC')->toString() . PHP_EOL;
// }
//
// $ts = TimestampSeconds::tryFromString('1735787045');
// if (!($ts instanceof Undefined)) {
//    echo $ts->toString() . PHP_EOL;
//    echo $ts->toInt() . PHP_EOL;
// }
//
// $tm = TimestampMilliseconds::tryFromString('1735787045123');
// if (!($tm instanceof Undefined)) {
//    echo $tm->toString() . PHP_EOL;
//    echo $tm->toInt() . PHP_EOL;
// }
