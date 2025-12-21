<?php

declare(strict_types=1);

namespace PhpTypedValues\Internal\Primitive\String;

use PhpTypedValues\Internal\Primitive\PrimitiveType;

/**
 * Base implementation for string-typed values.
 *
 * Provides common formatting helpers for value objects backed by strings.
 * Concrete string types extend this class and add domain-specific
 * validation/normalization.
 *
 * Example
 *  - $v = MyString::fromString('hello');
 *  - $v->toString(); // "hello"
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
abstract class StrType extends PrimitiveType implements StrTypeInterface
{
}
