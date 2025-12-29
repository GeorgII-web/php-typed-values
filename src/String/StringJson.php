<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const JSON_THROW_ON_ERROR;

use Exception;
use JsonException;
use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Base\Primitive\String\StrType;
use PhpTypedValues\Exception\JsonStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_scalar;
use function is_string;
use function json_decode;
use function sprintf;

/**
 * JSON text string.
 *
 * Validates the input using json_decode with JSON_THROW_ON_ERROR and stores
 * the original string on success. Helpers provide convenient decoding to
 * an object or array while reusing the same strict validation path.
 *
 * Example
 *  - $j = StringJson::fromString('{"a":1}');
 *    $j->toArray(); // ['a' => 1]
 *  - StringJson::fromString('{invalid}'); // throws JsonStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringJson extends StrType
{
    protected string $value;

    /**
     * @throws JsonStringTypeException
     */
    public function __construct(string $value)
    {
        static::assertJsonString($value);

        $this->value = $value;
    }

    /**
     * @throws JsonStringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throws JsonException
     */
    public function toObject(): object
    {
        return json_decode(json: $this->value, associative: false, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        return json_decode(json: $this->value, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonStringTypeException
     */
    protected static function assertJsonString(string $value): void
    {
        try {
            /**
             * Only validate; ignore the decoded result. Exceptions signal invalid JSON.
             *
             * @psalm-suppress UnusedFunctionCall
             */
            json_decode(json: $value, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonStringTypeException(sprintf('String "%s" has no valid JSON value', $value), 0, $e);
        }
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

    public function isEmpty(): bool
    {
        // JSON values are never empty by construction; constructor rejects empty strings
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                null === $value => static::fromString(''),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveType
     *
     * @param T $default
     *
     * @return static|T
     */
    public static function tryFromString(
        string $value,
        PrimitiveType $default = new Undefined(),
    ): static|PrimitiveType {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }
}
