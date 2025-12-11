<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use PhpTypedValues\Abstract\Array\ArrayType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 * @psalm-immutable
 */
final readonly class ArrayOfStrings extends ArrayType
{
    /**
     * @param StringNonEmpty[] $value
     *
     * @throws StringTypeException
     * @throws TypeException
     */
    public function __construct(
        private array $value,
    ) {
        if ($value === []) {
            throw new TypeException('Expected non-empty array');
        }

        foreach ($value as $item) {
            if (!$item instanceof StringNonEmpty) {
                throw new StringTypeException('Expected array of StringNonEmpty instance');
            }
        }
    }

    /**
     * @throws StringTypeException
     * @throws TypeException
     */
    public static function fromArray(array $value): self
    {
        $typed = [];
        foreach ($value as $item) {
            $typed[] = StringNonEmpty::fromString($item);
        }

        return new self($typed);
    }

    /**
     * @return StringNonEmpty[]
     */
    public function value(): array
    {
        return $this->value;
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    public function toStrings(): array
    {
        $result = [];
        foreach ($this->value as $item) {
            /** @var non-empty-string $value */
            $value = $item->toString();
            $result[] = $value;
        }

        /** @var non-empty-list<non-empty-string> $result */
        return $result;
    }

    /** @return non-empty-list<non-empty-string> */
    public function jsonSerialize(): array
    {
        return $this->toStrings();
    }

    /**
     * @return Traversable<StringNonEmpty>
     */
    public function getIterator(): Traversable
    {
        yield from $this->value;
    }

    public static function tryFromArray(array $value): static|Undefined
    {
        $typed = [];
        foreach ($value as $item) {
            $typed[] = StringNonEmpty::tryFromString($item);
        }

        return new self($typed);
    }
}
