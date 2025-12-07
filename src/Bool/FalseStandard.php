<?php

declare(strict_types=1);

namespace PhpTypedValues\Bool;

use PhpTypedValues\Abstract\Bool\BoolType;
use PhpTypedValues\Exception\BoolTypeException;
use PhpTypedValues\Exception\TypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

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
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromString(string $value)
    {
        try {
            return static::fromString($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
    }

    /**
     * @return static|\PhpTypedValues\Undefined\Alias\Undefined
     */
    public static function tryFromInt(int $value)
    {
        try {
            return static::fromInt($value);
        } catch (TypeException $exception) {
            return Undefined::create();
        }
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

    public function value(): bool
    {
        return $this->value;
    }
}
