<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const FILTER_VALIDATE_URL;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\UrlStringTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function filter_var;
use function sprintf;

/**
 * Absolute URL string (http/https recommended; uses FILTER_VALIDATE_URL for pragmatic validation).
 *
 * Example "https://example.com/path?x=1"
 *
 * @psalm-immutable
 */
class StringUrl extends StrType
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws UrlStringTypeException
     */
    public function __construct(string $value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new UrlStringTypeException(sprintf('Expected valid URL, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @throws UrlStringTypeException
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
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

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
