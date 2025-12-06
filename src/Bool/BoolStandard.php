<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Exception\BoolTypeException;

use function sprintf;

/**
 * Boolean value.
 *
 * Example "true"
 *
 * @psalm-immutable
 */
readonly class BoolStandard extends BoolType
{
    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromString(string $value): static
    {
        $lowerCaseValue = strtolower($value);

        if ($lowerCaseValue === 'true' || $lowerCaseValue === '1') {
            $boolValue = true;
        } elseif ($lowerCaseValue === 'false' || $lowerCaseValue === '0') {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected string "true"\"1" or "false"\"0", got "%s"', $value));
        }

        return new static($boolValue);
    }

    /**
     * @throws BoolTypeException
     */
    public static function fromInt(int $value): static
    {
        if ($value === 1) {
            $boolValue = true;
        } elseif ($value === 0) {
            $boolValue = false;
        } else {
            throw new BoolTypeException(sprintf('Expected int "1" or "0", got "%s"', $value));
        }

        return new static($boolValue);
    }

    public function value(): bool
    {
        return $this->value;
    }
}
