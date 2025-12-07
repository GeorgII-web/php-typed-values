<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\StringTypeException;

use function sprintf;

/**
 * Non-empty string value.
 *
 * Example "hello"
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
