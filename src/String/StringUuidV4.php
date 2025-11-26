<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use PhpTypedValues\Code\Contract\GenerateInterface;
use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;
use Random\RandomException;

use function chr;
use function ord;
use function preg_match;
use function sprintf;
use function strtolower;

/**
 * RFC 4122 version 4 (random).
 *
 * @psalm-immutable
 */
readonly class StringUuidV4 extends StrType implements GenerateInterface
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

    /**
     * @throws RandomException
     * @throws StringTypeException
     */
    public static function generate(): static
    {
        // UUID v4: 16 random bytes, with version and variant bits set as per RFC 4122
        $bytes = random_bytes(16);

        // Set version to 4 (0100xxxx)
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);

        // Set variant to RFC 4122 (10xxxxxx)
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        $hex = bin2hex($bytes);

        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );

        return new static($uuid);
    }
}
