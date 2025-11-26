<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;

use function preg_match;
use function sprintf;
use function strtolower;

/**
 * RFC 4122 version 4 (random).
 *
 * Example "550e8400-e29b-41d4-a716-446655440000"
 *
 * @psalm-immutable
 */
readonly class StringUuidV4 extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        // Normalize to lowercase for consistent representation
        $normalized = strtolower($value);

        // Most popular UUID type: RFC 4122 version 4 (random)
        // Format: xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx (hex, case-insensitive)
        if ($normalized === '') {
            // Provide a distinct message for empty input to ensure mutation testing can distinguish the branch
            throw new StringTypeException(sprintf('Expected non-empty UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized) !== 1) {
            throw new StringTypeException(sprintf('Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        $this->value = $normalized;
    }

    /**
     * @throws StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }
}
