<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;

/**
 * Non-empty string typed value.
 *
 * Ensures the wrapped string is not empty (length > 0). Useful for IDs,
 * names, and other values where an empty string is invalid.
 *
 * Example
 *  - $v = StringNonEmpty::fromString('hello');
 *    $v->value(); // 'hello'
 *  - StringNonEmpty::fromString(''); // throws StringTypeException
 *
 * @psalm-immutable
 */
class StringNonEmpty extends StrType
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if ($value === '') {
            throw new StringTypeException(sprintf('Expected non-empty string, got "%s"', $value));
        }

        $this->value = $value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @throws StringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
