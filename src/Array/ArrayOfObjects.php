<?php

declare(strict_types=1);

namespace PhpTypedValues\Array;

use JsonSerializable;
use PhpTypedValues\Abstract\Array\ArrayType;
use PhpTypedValues\Abstract\Primitive\PrimitiveType;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Traversable;

use function count;
use function is_object;

/**
 * Immutable collection of Primitive type classes.
 *
 * @template TItem of PrimitiveType
 *
 * @template-extends ArrayType<TItem>
 *
 * @psalm-immutable
 */
class ArrayOfObjects extends ArrayType
{
    /**
     * @var list<TItem>
     * @readonly
     */
    private array $value;

    /**
     * @param list<TItem> $value
     *
     * @throws ArrayTypeException
     */
    public function __construct(array $value)
    {
        foreach ($value as $item) {
            if (!is_object($item)) {
                throw new ArrayTypeException('Expected array of Object instances');
            }
        }

        $this->value = $value;
    }

    /**
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     * @return static
     */
    public static function fromArray(array $value)
    {
        /** @var list<TItem> $value */
        return new static($value);
    }

    /**
     * @param list<mixed> $value
     *
     * @throws ArrayTypeException
     * @return static
     */
    public static function tryFromArray(array $value)
    {
        $valueWithUndefined = [];
        foreach ($value as $item) {
            if (is_object($item)) {
                $valueWithUndefined[] = $item;
            } else {
                $valueWithUndefined[] = Undefined::create();
            }
        }

        /** @var list<TItem> $valueWithUndefined */
        return new static($valueWithUndefined);
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
     * @throws ArrayTypeException
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            if (!$item instanceof JsonSerializable) {
                throw new ArrayTypeException('Conversion to array of scalars failed, should implement JsonSerializable interface');
            }

            $result[] = $item->jsonSerialize();
        }

        return $result;
    }

    /**
     * JSON serialization helper.
     *
     * @throws ArrayTypeException
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function isUndefined(): bool
    {
        $items = $this->value();

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
        foreach ($this->value() as $item) {
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

        foreach ($this->value() as $item) {
            if (!$item instanceof Undefined) {
                $result[] = $item;
            }
        }

        /** @var list<TItem> $result */
        return $result;
    }
}
