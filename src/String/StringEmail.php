<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const FILTER_VALIDATE_EMAIL;

use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\EmailStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function filter_var;
use function sprintf;

/**
 * Email address string (RFC 5322 pragmatic validation).
 *
 * Uses PHP's FILTER_VALIDATE_EMAIL to validate an address string. The value is
 * stored as provided and must be non-empty.
 *
 * Example
 *  - $e = StringEmail::fromString('user@example.com');
 *    (string) $e; // 'user@example.com'
 *  - StringEmail::fromString('not-an-email'); // throws EmailStringTypeException
 *
 * @psalm-immutable
 */
class StringEmail extends StrType
{
    /** @var non-empty-string
     * @readonly */
    protected string $value;

    /**
     * @throws EmailStringTypeException
     */
    public function __construct(string $value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new EmailStringTypeException(sprintf('Expected valid email address, got "%s"', $value));
        }

        /** @var non-empty-string $value */
        $this->value = $value;
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     * @param mixed $value
     */
    public static function tryFromMixed($value)
    {
        try {
            return static::fromString(
                static::convertMixedToString($value)
            );
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @throws EmailStringTypeException
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

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
