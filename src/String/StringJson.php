<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const JSON_THROW_ON_ERROR;

use JsonException;
use PhpTypedValues\Abstract\String\StrType;
use PhpTypedValues\Exception\JsonStringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

use function json_decode;
use function sprintf;

/**
 * Represents a valid JSON text.
 *
 * Example '{"a":1}'
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

    public static function tryFromString(string $value): static|Undefined
    {
        try {
            return static::fromString($value);
        } catch (TypeException) {
            return Undefined::create();
        }
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
}
