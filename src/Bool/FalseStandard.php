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
class FalseStandard extends BoolType
{
    /**
     * @readonly
     */
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
     * @return static
     */
    public static function fromString(string $value)
    {
        $v = strtolower(trim($value));
        if ($v === 'false' || $v === '0' || $v === 'no' || $v === 'off' || $v === 'n') {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected string representing false, got "%s"', $value));
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromInt(int $value)
    {
        if ($value === 0) {
            return new static(false);
        }

        throw new BoolTypeException(sprintf('Expected int "0" for false, got "%s"', $value));
    }

    /**
     * @return false
     */
    public function value(): bool
    {
        return $this->value;
    }

    /**
     * @return false
     */
    public function jsonSerialize(): bool
    {
        return $this->value();
    }

    /**
     * @throws BoolTypeException
     * @return static
     */
    public static function fromBool(bool $value)
    {
        return new static($value);
    }
}
