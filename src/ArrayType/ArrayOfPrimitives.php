<?php

declare(strict_types=1);

namespace PhpTypedValues\ArrayType;

use PhpTypedValues\Base\ArrayType\ArrayTypeAbstract;
use PhpTypedValues\Base\Primitive\PrimitiveTypeInterface;
use PhpTypedValues\Exception\ArrayType\PrimitivesArrayTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use Traversable;

use function count;

/**
 * Immutable collection of primitives.
 *
 * Example
 *  - $v = ArrayOfPrimitives::fromArray([]);
 *    $v->isEmpty(); // true
 *  - $v = ArrayOfPrimitives::fromArray([IntegerPositive::fromInt(1)]);
 *    $v->isEmpty(); // false
 *
 * @template TItem of PrimitiveTypeInterface
 *
 * @template-extends ArrayTypeAbstract<TItem>
 *
 * @psalm-immutable
 */
class ArrayOfPrimitives extends ArrayTypeAbstract
{
    /**
     * @var list<TItem>
     * @readonly
     */
    private array $value;

    /**
     * @param list<TItem> $value
     *
     * @throws PrimitivesArrayTypeException
     */
    public function __construct(array $value)
    {
        foreach ($value as $item) {
            if (!$item instanceof PrimitiveTypeInterface) {
                throw new PrimitivesArrayTypeException('Expected array of PrimitiveTypeInterface instances');
            }
        }

        $this->value = $value;
    }

    /**
     * @return non-negative-int
     */
    public function count(): int
    {
        return count($this->value);
    }

    /**
     * @psalm-pure
     *
     * @param list<mixed> $value
     *
     * @throws PrimitivesArrayTypeException
     * @return static
     */
    public static function fromArray(array $value): self
    {
        /** @var list<TItem> $value */
        return new static($value);
    }

    /**
     * @no-named-arguments
     *
     * @throws PrimitivesArrayTypeException
     * @return static
     */
    public static function fromItems(PrimitiveTypeInterface ...$items): self
    {
        /** @var list<TItem> $items */
        return new static($items);
    }

    /**
     * @psalm-return list<TItem>
     */
    public function getDefinedItems(): array
    {
        $result = [];

        foreach ($this->value as $item) {
            if (!$item->isUndefined()) {
                $result[] = $item;
            }
        }

        /** @var list<TItem> $result */
        return $result;
    }

    /**
     * @return Traversable<int, TItem>
     */
    public function getIterator(): Traversable
    {
        yield from $this->value;
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

    public function isEmpty(): bool
    {
        return $this->count() === 0;
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
        $items = $this->value;

        if ($items === []) {
            return false;
        }

        foreach ($items as $item) {
            if (!$item->isUndefined()) {
                return false;
            }
        }

        return true;
    }

    /**
     * JSON serialization helper.
     *
     * @psalm-mutation-free
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @psalm-mutation-free
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            /** @psalm-suppress ImpureMethodCall */
            $result[] = $item->jsonSerialize();
        }

        return $result;
    }

    /**
     * @return list<TItem>
     */
    public function value(): array
    {
        return $this->value;
    }
}
