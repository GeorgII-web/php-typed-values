<?php

declare(strict_types=1);

namespace Tests\Unit\Base\Primitive\Bool;

use PhpTypedValues\Base\Primitive\Bool\BoolType;
use PhpTypedValues\Bool\BoolStandard;
use PhpTypedValues\Undefined\Alias\Undefined;
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
    expect($instance)->toBeInstanceOf(Undefined::class);
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

it('converts mixed values to correct boolean state', function (mixed $input, bool $expected): void {
    $result = BoolStandard::tryFromMixed($input);

    expect($result)->toBeInstanceOf(BoolStandard::class)
        ->and($result->value())->toBe($expected);
})->with([
    // Booleans
    ['input' => true, 'expected' => true],
    ['input' => false, 'expected' => false],
    // Integers
    ['input' => 1, 'expected' => true],
    ['input' => 0, 'expected' => false],
    // Floats (Strict)
    ['input' => 1.0, 'expected' => true],
    ['input' => 0.0, 'expected' => false],
    // Strings (True-like)
    ['input' => 'true', 'expected' => true],
    ['input' => '1', 'expected' => true],
    ['input' => 'yes', 'expected' => true],
    ['input' => 'on', 'expected' => true],
    ['input' => 'y', 'expected' => true],
    // Strings (False-like)
    ['input' => 'false', 'expected' => false],
    ['input' => '0', 'expected' => false],
    ['input' => 'no', 'expected' => false],
    ['input' => 'off', 'expected' => false],
    ['input' => 'n', 'expected' => false],
    // Value Objects
    ['input' => BoolStandard::fromBool(true), 'expected' => true],
    ['input' => BoolStandard::fromBool(false), 'expected' => false],
]);
