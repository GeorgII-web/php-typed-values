<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Exception\BoolTypeException;

use function sprintf;
use function strtolower;
use function trim;

/**
 * Literal boolean false typed value.
 *
 * Accepts common false-like representations in factories:
 *  - Strings: "false", "0", "no", "off", "n" (case-insensitive)
 *  - Ints: 0
 *
 * Example
 *  - $f = FalseStandard::fromString('Off');
 *    $f->value(); // false
 *  - $f = FalseStandard::fromInt(0);
 *    $f->toString(); // "false"
 *
 * @psalm-immutable
 */
readonly class FalseStandard extends BoolType
{
    protected false $value;

    /**
     * @throws BoolTypeException
     */
    public function __construct(bool $value)
    {
        if ($value !== false) {
            throw new BoolTypeException('Expected false literal, got "true"');
        }

        $this->value = false;
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromString(string $value): static
    {
        $v = strtolower(trim($value));
        if ($v === 'false' || $v === '0' || $v === 'no' || $v === 'off' || $v === 'n') {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected string representing false, got "%s"', $value));
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        if ($value === 0) {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected int "0" for false, got "%s"', $value));
    }

    public function value(): false
    {
        return $this->value;
    }

    public function jsonSerialize(): false
    {
        return $this->value();
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromBool(bool $value): static
    {
        return new static($value);
    }
}
