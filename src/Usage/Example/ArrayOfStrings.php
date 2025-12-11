<?php

declare(strict_types=1);

namespace PhpTypedValues\Usage\Example;

require_once 'vendor/autoload.php';

use Generator;
use PhpTypedValues\Abstract\Array\ArrayType;
use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Exception\UndefinedTypeException;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class ArrayOfStrings extends ArrayType
{
    /**
     * @param StringNonEmpty|Undefined[] $value
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
            if ((!$item instanceof StringNonEmpty) && (!$item instanceof Undefined)) {
                throw new StringTypeException('Expected array of StringNonEmpty or Undefined instance');
            }
        }
    }

    /**
     * @throws StringTypeException
     * @throws TypeException
     */
    public static function fromArray(array $value): static
    {
        $typed = [];
        foreach ($value as $item) {
            $typed[] = StringNonEmpty::fromString($item);
        }

        return new self($typed);
    }

    /**
     * @return StringNonEmpty|Undefined[]
     */
    public function value(): array
    {
        return $this->value;
    }

    /**
     * @return non-empty-list<non-empty-string>
     *
     * @throws UndefinedTypeException
     */
    public function toArray(): array
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
        return $this->toArray();
    }

    /**
     * @return Generator<StringNonEmpty>
     */
    public function getIterator(): Generator
    {
        yield from $this->value;
    }

    /**
     * @throws StringTypeException
     * @throws TypeException
     */
    public static function tryFromArray(array $value): static|Undefined
    {
        $typed = [];
        foreach ($value as $item) {
            $typed[] = StringNonEmpty::tryFromMixed($item);
        }

        return new static($typed);
    }
}
