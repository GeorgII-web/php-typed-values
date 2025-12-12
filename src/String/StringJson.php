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
class StringJson extends StrType
{
    /**
     * @readonly
     */
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
     * @throws JsonStringTypeException
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

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throws JsonException
     */
    public function toObject(): object
    {
        return json_decode($this->value, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        return json_decode($this->value, true, 512, JSON_THROW_ON_ERROR);
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
            json_decode($value, null, 512, JSON_THROW_ON_ERROR);
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
}
