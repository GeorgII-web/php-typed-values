<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use Generator;
use PhpTypedValues\Abstract\Array\ArrayType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringNonEmpty;

/**
 * Immutable collection of non-empty strings represented as `StringNonEmpty`.
 *
 * Provides factory helpers to construct from raw arrays, exposes the typed
 * items via `value()`, supports iteration, and can be converted to a plain
 * array or JSON.
 *
 * Example
 *  - $a = ArrayOfStrings::fromArray(['foo', 'bar']);
 *    $a->toArray(); // ['foo', 'bar']
 *    foreach ($a as $item) { $item->toString(); // 'foo', then 'bar' }
 *  - ArrayOfStrings::fromArray([]); // throws TypeException('Expected non-empty array')
 *  - ArrayOfStrings::fromArray([123, 45.6]); // casts to strings → ['123', '45.6']
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @extends ArrayType<StringNonEmpty>
 *
 * @psalm-immutable
 */
final class ArrayOfStrings extends ArrayType
{
    /**
     * @var non-empty-list<StringNonEmpty>
     * @readonly
     */
    private array $value;
    /**
     * Creates a new collection from a non-empty list of `StringNonEmpty`.
     *
     * @param non-empty-list<StringNonEmpty> $value items must be instances of `StringNonEmpty`
     *
     * @throws StringTypeException
     * @throws TypeException
     */
    public function __construct(
        array $value
    ) {
        /** @var non-empty-list<StringNonEmpty> */
        $this->value = $value;
        if ($value === []) {
            throw new TypeException('Expected non-empty array');
        }

        foreach ($value as $item) {
            if (!$item instanceof StringNonEmpty) {
                throw new StringTypeException('Expected array of StringNonEmpty or Undefined instance');
            }
        }
    }

    /**
     * Creates a collection from a non-empty list of raw values by casting each
     * value to string and validating it as `StringNonEmpty`.
     *
     * @param array $value raw values to convert
     *
     * @psalm-param list<mixed> $value
     *
     * @throws StringTypeException
     * @throws TypeException
     * @return static
     */
    public static function fromArray(array $value)
    {
        if ($value === []) {
            throw new TypeException('Expected non-empty array');
        }

        $typed = [];
        foreach ($value as $item) {
            $typed[] = StringNonEmpty::fromString((string) $item);
        }

        /** @var non-empty-list<StringNonEmpty> $typed */
        return new self($typed);
    }

    /**
     * Same as `fromArray()` but intended for scenarios where late/optional
     * failure might be desirable. Current implementation mirrors `fromArray()`.
     *
     * @param array $value raw values to convert
     *
     * @psalm-param list<mixed> $value
     *
     * @throws StringTypeException
     * @throws TypeException
     * @return static
     */
    public static function tryFromArray(array $value)
    {
        return static::fromArray($value);
    }

    /**
     * Returns the underlying typed items.
     *
     * @psalm-return non-empty-list<StringNonEmpty>
     */
    public function value(): array
    {
        return $this->value;
    }

    /**
     * Converts the collection to a non-empty list of raw strings.
     *
     * @return non-empty-list<non-empty-string>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            /** @var non-empty-string $str */
            $str = $item->toString();
            $result[] = $str;
        }

        /** @var non-empty-list<non-empty-string> $result */
        return $result;
    }

    /**
     * JSON serialization proxy that returns the same as `toArray()`.
     *
     * @return non-empty-list<non-empty-string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Iterates over the underlying `StringNonEmpty` items.
     *
     * @return Generator<int, StringNonEmpty>
     */
    public function getIterator(): Generator
    {
        yield from $this->value;
    }
}
