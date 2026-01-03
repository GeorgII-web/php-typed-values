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
 * Valid JSON string.
 *
 * Validates input using json_decode with JSON_THROW_ON_ERROR. The original
 * string is preserved and must be non-empty and syntactically valid JSON.
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
     * @var non-empty-string
     * @readonly
     */
    protected string $value;

    /**
     * @throws JsonStringTypeException
     */
    public function __construct(string $value)
    {
        $this->value = static::getJsonStringFromString($value);
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
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throws JsonException
     * @return mixed
     */
    public function toObject()
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
     * @return non-empty-string
     *
     * @throws JsonStringTypeException
     */
    protected static function getJsonStringFromString(string $value): string
    {
        try {
            if ($value === '') {
                throw new JsonStringTypeException('Empty string cannot be a valid JSON');
            }

            /**
             * Only validate; ignore the decoded result. Exceptions signal invalid JSON.
             *
             * @psalm-suppress UnusedFunctionCall
             */
            json_decode($value, null, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonStringTypeException(sprintf('String "%s" has no valid JSON value', $value), 0, $e);
        }

        return $value;
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function isTypeOf(string ...$classNames): bool
    {
        foreach ($classNames as $className) {
            if ($this instanceof $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return non-empty-string
     */
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
     * @param mixed $value
     */
    public static function tryFromMixed(
        $value,
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            switch (true) {
                case is_string($value):
                    return static::fromString($value);
                case is_object($value) && method_exists($value, '__toString'):
                case is_scalar($value):
                    return static::fromString((string) $value);
                case null === $value:
                    return static::fromString('null');
                default:
                    throw new TypeException('Value cannot be cast to string');
            }
        } catch (Exception $exception) {
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
        PrimitiveType $default = null
    ) {
        $default ??= new Undefined();
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception $exception) {
            /** @var T */
            return $default;
        }
    }
}
