<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Generic string typed value.
 *
 * Wraps any PHP string without additional validation and provides
 * convenient factory and formatting helpers.
 *
 * Example
 *  - $v = StringStandard::fromString('hello');
 *    $v->toString(); // "hello"
 *  - (string) StringStandard::fromString('x'); // "x"
 *
 * @psalm-immutable
 */
class StringStandard extends StrType
{
    /**
     * @readonly
     */
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        return static::fromString($value);
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
