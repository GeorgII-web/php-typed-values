<?php

declare(strict_types=1);

namespace PhpTypedValues\String\Specific;

use const JSON_THROW_ON_ERROR;

use Exception;
use JsonException;
use PhpTypedValues\Base\Primitive\PrimitiveTypeAbstract;
use PhpTypedValues\Base\Primitive\String\StringTypeAbstract;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\JsonStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;

use function is_bool;
use function is_float;
use function is_int;
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
 *  Valid JSON includes:
 *  - Objects: {"key": "value"}, {}
 *  - Arrays: [1, 2, 3], []
 *  - Strings: "text", "" (empty string in quotes), " "
 *  - Numbers: 123, 3.14, -42, 0.5e10
 *  - Booleans: true, false
 *  - Null: null
 *
 * Example
 *  - $j = StringJson::fromString('{"a":1}');
 *    $j->toArray(); // ['a' => 1]
 *  - StringJson::fromString('{invalid}'); // throws JsonStringTypeException
 *
 * @psalm-immutable
 */
readonly class StringJson extends StringTypeAbstract
{
    /**
     * @var non-empty-string
     */
    protected string $value;

    /**
     * @throws JsonStringTypeException
     */
    public function __construct(string $value)
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
            json_decode(json: $value, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new JsonStringTypeException(sprintf('String "%s" has no valid JSON value', $value), 0, $e);
        }

        $this->value = $value;
    }

    /**
     * @throws JsonStringTypeException
     *
     * @psalm-pure
     */
    public static function fromBool(bool $value): static
    {
        return new static(static::boolToString($value));
    }

    /**
     * @throws FloatTypeException
     * @throws JsonStringTypeException
     * @throws StringTypeException
     *
     * @psalm-pure
     */
    public static function fromFloat(float $value): static
    {
        return new static(static::floatToString($value));
    }

    /**
     * @throws JsonStringTypeException
     *
     * @psalm-pure
     */
    public static function fromInt(int $value): static
    {
        return new static(static::intToString($value));
    }

    /**
     * @throws JsonStringTypeException
     *
     * @psalm-pure
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function isEmpty(): bool
    {
        return false;
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

    public function isUndefined(): bool
    {
        return false;
    }

    /**
     * @return non-empty-string
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        return json_decode(json: $this->value, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @throws IntegerTypeException
     */
    public function toBool(): bool
    {
        return static::stringToBool($this->value());
    }

    /**
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    public function toFloat(): float
    {
        return static::stringToFloat($this->value());
    }

    /**
     * @throws StringTypeException
     */
    public function toInt(): int
    {
        return static::stringToInt($this->value());
    }

    /**
     * @throws JsonException
     */
    public function toObject(): mixed
    {
        return json_decode(json: $this->value, associative: false, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value();
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromBool(
        bool $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromBool($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromFloat(
        float $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromFloat($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromInt(
        int $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromInt($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromMixed(
        mixed $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return match (true) {
                is_string($value) => static::fromString($value),
                is_float($value) => static::fromFloat($value),
                is_int($value) => static::fromInt($value),
                //                ($value instanceof self) => static::fromString($value->value()),
                is_bool($value) => static::fromBool($value),
                $value instanceof Stringable, is_scalar($value) => static::fromString((string) $value),
                null === $value => static::fromString('null'),
                default => throw new TypeException('Value cannot be cast to string'),
            };
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @template T of PrimitiveTypeAbstract
     *
     * @param T $default
     *
     * @return static|T
     *
     * @psalm-pure
     */
    public static function tryFromString(
        string $value,
        PrimitiveTypeAbstract $default = new Undefined(),
    ): static|PrimitiveTypeAbstract {
        try {
            /** @var static */
            return static::fromString($value);
        } catch (Exception) {
            /** @var T */
            return $default;
        }
    }

    /**
     * @return non-empty-string
     */
    public function value(): string
    {
        return $this->value;
    }
}
