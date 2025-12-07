<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function sprintf;
use function trim;

/**
 * Non-blank string value (not empty and not only whitespace).
 *
 * Example "hello" or " hi ".
 *
 * @psalm-immutable
 */
class StringNonBlank extends StrType
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        if (trim($value) === '') {
            throw new StringTypeException(sprintf('Expected non-blank string, got "%s"', $value));
        }

        /** @var non-empty-string $value */
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
