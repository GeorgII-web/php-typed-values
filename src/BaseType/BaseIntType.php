<?php

declare(strict_types=1);

namespace PhpTypedValues\BaseType;

use Override;
use PhpTypedValues\Contract\BaseTypeInterface;
use PhpTypedValues\Contract\IntTypeInterface;
use PhpTypedValues\Exception\IntegerTypeException;

use function sprintf;

/**
 * @psalm-immutable
 */
abstract readonly class BaseIntType implements BaseTypeInterface, IntTypeInterface
{
    protected int $value;

    /**
     * @throws IntegerTypeException
     */
    public function __construct(int $value)
    {
        $this->assert($value);
        $this->value = $value;
    }

    #[Override]
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public static function fromString(string $value): self
    {
        if (!preg_match('/^-?\d+$/', $value)) {
            throw new IntegerTypeException('String has no valid integer');
        }

        return new static((int) $value);
    }

    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public static function fromInt(int $value): self
    {
        return new static($value);
    }

    #[Override]
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public function assertLessThan(int $value, int $limit, bool $inclusive = false): void
    {
        if ($inclusive && $value > $limit) {
            throw new IntegerTypeException(sprintf('Value "%d" is greater than maximum "%d"', $value, $limit));
        }

        if (!$inclusive && $value >= $limit) {
            throw new IntegerTypeException(sprintf('Value "%d" is greater than or equal to maximum "%d"', $value, $limit));
        }
    }

    /**
     * @throws IntegerTypeException
     */
    #[Override]
    public function assertGreaterThan(int $value, int $limit, bool $inclusive = false): void
    {
        if ($inclusive && $value < $limit) {
            throw new IntegerTypeException(sprintf('Value "%d" is less than minimum "%d"', $value, $limit));
        }

        if (!$inclusive && $value <= $limit) {
            throw new IntegerTypeException(sprintf('Value "%d" is less than or equal to minimum "%d"', $value, $limit));
        }
    }

    /**
     * Domain-specific assertion to validate provided integer value.
     *
     * @throws IntegerTypeException
     */
    #[Override]
    abstract public function assert(int $value): void;
}
