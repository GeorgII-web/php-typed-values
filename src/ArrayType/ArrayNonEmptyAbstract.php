<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use JsonSerializable;
use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Exception\Array\ArrayTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Stringable;
use Traversable;

use function count;
use function is_scalar;
use function sprintf;

/**
 * Immutable non-empty array.
 *
 * @template TItem of object
 *
 * @template-extends ArrayTypeAbstract<TItem>
 *
 * @psalm-immutable
 */
readonly class ArrayNonEmptyAbstract extends ArrayTypeAbstract
{
    /**
     * @var list<TItem>
     */
    private array $value;

    /**
     * @param list<TItem> $value
     *
     * @throws ArrayTypeException
     */
    public function __construct(array $value)
    {
        if ($value === []) {
            throw new ArrayTypeException('Expected non-empty array');
        }

        $this->value = $value;
    }

    /**
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     */
    public static function fromArray(array $value): static
    {
        /** @var list<TItem> $value */
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
     * @return list<TItem>
     */
    public function value(): array
    {
        return $this->value;
    }

    /**
     * @return Traversable<int, TItem>
     */
    public function getIterator(): Traversable
    {
        yield from $this->value;
    }

    /**
     * @return non-negative-int
     */
    public function count(): int
    {
        return count($this->value);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @psalm-mutation-free
     *
     * @throws ArrayTypeException
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            // 1. Handle objects that know how to serialize themselves
            if ($item instanceof JsonSerializable) {
                /** @psalm-suppress ImpureMethodCall */
                $result[] = $item->jsonSerialize();
                continue;
            }

            // 2. Handle native PHP scalars (string, int, float, bool) or null
            if (is_scalar($item) || $item === null) {
                $result[] = $item;
                continue;
            }

            // 3. Fallback for objects that don't implement JsonSerializable but might be Stringable
            if ($item instanceof Stringable) {
                $result[] = $item->__toString();
                continue;
            }

            throw new ArrayTypeException(sprintf('Item of type "%s" cannot be converted to a scalar or JSON-serializable value.', get_debug_type($item)));
        }

        return $result;
    }

    /**
     * JSON serialization helper.
     *
     * @throws ArrayTypeException
     *
     * @psalm-mutation-free
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function isUndefined(): bool
    {
        $items = $this->value;

        if ($items === []) {
            return false;
        }

        foreach ($items as $item) {
            if (!$item instanceof Undefined) {
                return false;
            }
        }

        return true;
    }

    public function hasUndefined(): bool
    {
        foreach ($this->value as $item) {
            if ($item instanceof Undefined) {
                return true;
            }
        }

        return false;
    }

    /**
     * @psalm-return list<TItem>
     */
    public function getDefinedItems(): array
    {
        $result = [];

        foreach ($this->value as $item) {
            if (!$item instanceof Undefined) {
                $result[] = $item;
            }
        }

        /** @var list<TItem> $result */
        return $result;
    }
}
