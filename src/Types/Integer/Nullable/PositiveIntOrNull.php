<?php

declare(strict_types=1);

namespace GeorgiiWeb\PhpTypedValues\Types\Integer\Nullable;

use GeorgiiWeb\PhpTypedValues\Types\Integer\PositiveInt;

/**
 * @extends PositiveInt
 *
 * @psalm-immutable
 */
class PositiveIntOrNull extends PositiveInt
{
    protected function assertValid(mixed $value): void
    {
        if ($value === null) {
            return; // null is allowed
        }
        parent::assertValid($value);
    }

    public static function fromString(string $value): static
    {
        $trim = trim($value);
        if ($trim === '' || strtolower($trim) === 'null') {
            return new static(null);
        }

        return parent::fromString($value);
    }

    /**
     * @psalm-external-mutation-free
     */
    public function toString(): string
    {
        return $this->getValue() === null ? 'null' : parent::toString();
    }
}
