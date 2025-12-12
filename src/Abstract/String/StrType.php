<?php

declare(strict_types=1);

namespace PhpTypedValues\Abstract\String;

use PhpTypedValues\Abstract\AbstractType;

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
abstract class StrType extends AbstractType implements StrTypeInterface
{
}
