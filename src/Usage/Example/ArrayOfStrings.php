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
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @extends ArrayType<StringNonEmpty>
 *
 * @psalm-immutable
 */
final readonly class ArrayOfStrings extends ArrayType
{
    /**
     * @param non-empty-list<StringNonEmpty> $value
     *
     * @throws StringTypeException
     * @throws TypeException
     */
    public function __construct(
        /** @var non-empty-list<StringNonEmpty> */
        private array $value,
    ) {
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
     * @psalm-param list<mixed> $value
     *
     * @throws StringTypeException
     * @throws TypeException
     */
    public static function fromArray(array $value): static
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
     * @psalm-param list<mixed> $value
     *
     * @throws StringTypeException
     * @throws TypeException
     */
    public static function tryFromArray(array $value): static
    {
        return static::fromArray($value);
    }

    /**
     * @psalm-return non-empty-list<StringNonEmpty>
     */
    public function value(): array
    {
        return $this->value;
    }

    /**
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
     * @return non-empty-list<non-empty-string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return Generator<int, StringNonEmpty>
     */
    public function getIterator(): Generator
    {
        yield from $this->value;
    }
}
