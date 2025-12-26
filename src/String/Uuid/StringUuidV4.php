<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Uuid;

use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\UuidStringTypeException;

use function preg_match;
use function sprintf;
use function strtolower;

/**
 * UUID version 4 (random) string.
 *
 * Accepts a RFC 4122 UUID v4 in canonical dashed form. Input is treated
 * case‑insensitively and normalized to lowercase for consistent storage and
 * comparison.
 *
 * Example
 *  - $u = StringUuidV4::fromString('550E8400-E29B-41D4-A716-446655440000');
 *    $u->toString(); // '550e8400-e29b-41d4-a716-446655440000'
 *  - StringUuidV4::fromString('not-a-uuid'); // throws UuidStringTypeException
 *
 * @method        false            isUndefined()
 * @method        non-empty-string value()
 * @method        bool             isEmpty()
 * @method        string           toString()
 * @method static static|mixed     tryFromString(string $value, mixed $default = null)
 * @method static static|mixed     tryFromMixed(mixed $value, mixed $default = null)
 *
 * @psalm-immutable
 */
readonly class StringUuidV4 extends StrType
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws UuidStringTypeException
     */
    public function __construct(string $value)
    {
        // Normalize to lowercase for consistent representation
        $normalized = strtolower($value);

        // Most popular UUID type: RFC 4122 version 4 (random)
        // Format: xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx (hex, case-insensitive)
        if ($normalized === '') {
            // Provide a distinct message for empty input to ensure mutation testing can distinguish the branch
            throw new UuidStringTypeException(sprintf('Expected non-empty UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized) !== 1) {
            throw new UuidStringTypeException(sprintf('Expected UUID v4 (xxxxxxxx-xxxx-4xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        $this->value = $normalized;
    }

    /**
     * @throws UuidStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
