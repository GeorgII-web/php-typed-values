<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayType;
use PhpTypedValues\Exception\ArrayTypeException;
use Traversable;

use function count;

/**
 * Immutable empty collection.
 *
 * @extends ArrayType<never>
 *
 * @psalm-immutable
 */
class ArrayEmpty extends ArrayType
{
    /**
     * @throws ArrayTypeException
     */
    public function __construct(array $value)
    {
        if (count($value) > 0) {
            throw new ArrayTypeException('Expected empty array');
        }
    }

    /**
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     * @return static
     */
    public static function fromArray(array $value)
    {
        /** @var array $value */
        return new static($value);
    }

    /**
     * @return list<never>
     */
    public function value(): array
    {
        return [];
    }

    public function getIterator(): Traversable
    {
        yield from [];
    }

    /**
     * @return non-negative-int
     */
    public function count(): int
    {
        return 0;
    }

    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * @psalm-mutation-free
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * JSON serialization helper.
     *
     * @psalm-mutation-free
     */
    public function jsonSerialize(): array
    {
        return [];
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function hasUndefined(): bool
    {
        return false;
    }

    /**
     * @return list<never>
     */
    public function getDefinedItems(): array
    {
        return [];
    }
}
