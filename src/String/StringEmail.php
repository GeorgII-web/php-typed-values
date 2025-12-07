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
readonly class StringEmail extends StrType
{
    /** @var non-empty-string */
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
     * @throws EmailStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
