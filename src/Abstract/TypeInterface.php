<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract;

/**
 * Base contract for all typed value objects in this library.
 *
 * Typed values are small immutable wrappers around primitive data with
 * validation and handy factory/formatting helpers.
 *
 * Example
 *  - A concrete string type will implement this interface via StrType and expose:
 *    $v = MyStringType::fromString("hello");
 *    $v->toString(); // "hello"
 *
 * @psalm-immutable
 */
interface TypeInterface
{
}
