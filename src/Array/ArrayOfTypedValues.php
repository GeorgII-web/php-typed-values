<?php

declare(strict_types=1);

namespace PhpTypedValues\Array;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use PhpTypedValues\Abstract\Array\ArrayTypeInterface;
use PhpTypedValues\Abstract\Primitive\PrimitiveType;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Exception\TypeException;
use Traversable;

use function count;

/**
 * Immutable collection of Primitive type classes.
 *
 * @template TItem of PrimitiveType
 *
 * @implements IteratorAggregate<int, TItem>
 *
 * @template-implements ArrayTypeInterface<TItem>
 *
 * @psalm-immutable
 */
readonly class ArrayOfTypedValues implements IteratorAggregate, JsonSerializable, Countable
{
    /**
     * @param list<TItem> $value
     *
     * @throws ArrayTypeException
     */
    public function __construct(private array $value)
    {
        foreach ($value as $item) {
            if (!$item instanceof PrimitiveType) {
                throw new ArrayTypeException('Expected array of AbstractType instances');
            }
        }
    }

    /**
     * @param list<mixed>           $value
     * @param callable(mixed):TItem $cast  Callback that returns a concrete AbstractType
     *
     * @return ArrayOfTypedValues<TItem>
     *
     * @throws ArrayTypeException
     */
    public static function fromArray(array $value, callable $cast): self
    {
        $typedItems = [];
        foreach ($value as $item) {
            try {
                $typedItem = $cast($item);
            } catch (TypeException $e) {
                throw new ArrayTypeException('Callback conversion failed. ' . $e->getMessage(), 0, $e);
            }

            if (!$typedItem instanceof PrimitiveType) {
                throw new ArrayTypeException('Expected $cast to return an AbstractType instance');
            }

            $typedItems[] = $typedItem;
        }

        /** @var list<TItem> $typedItems */
        return new self($typedItems);
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

    public function count(): int
    {
        return count($this->value);
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            $result[] = $item->value();
        }

        return $result;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
