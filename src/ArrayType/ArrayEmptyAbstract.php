<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Exception\Array\ArrayTypeException;
use Traversable;

use function count;

/**
 * Immutable empty collection.
 *
 * @extends ArrayTypeAbstract<never>
 *
 * @psalm-immutable
 */
readonly class ArrayEmptyAbstract extends ArrayTypeAbstract
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
     */
    public static function fromArray(array $value): static
    {
        /** @var array $value */
        return new static($value);
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
