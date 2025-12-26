<?php

declare(strict_types=1);

namespace Tests\Unit\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use stdClass;
use Stringable;

/**
 * @internal
 *
 * @coversNothing
 */
readonly class BoolTypeTest extends BoolType
{
    public function __construct(public string $lastValue = '')
    {
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }

    public static function fromInt(int $value): static
    {
        return new self();
    }

    public static function fromBool(bool $value): static
    {
        return new self();
    }

    public function value(): bool
    {
        return true;
    }

    public function jsonSerialize(): bool
    {
        return true;
    }
}

it('BoolType::convertMixedToString correctly handles null', function (): void {
    $instance = BoolTypeTest::tryFromMixed(null);
    expect($instance->lastValue)->toBe('');
});

it('BoolType::convertMixedToString correctly handles Stringable', function (): void {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'yes';
        }
    };
    $instance = BoolTypeTest::tryFromMixed($stringable);
    expect($instance->lastValue)->toBe('yes');
});

it('BoolType::convertMixedToString throws TypeException for non-stringable objects', function (): void {
    $result = BoolTypeTest::tryFromMixed(new stdClass(), 'default');
    expect($result)->toBe('default');
});
