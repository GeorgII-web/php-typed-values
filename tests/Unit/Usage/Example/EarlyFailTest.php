<?php

declare(strict_types=1);

use PhpTypedValues\Base\ValueObjectInterface;
use PhpTypedValues\Exception\Float\FloatTypeException;
use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;

/**
 * Example of strict early‑fail semantics for constructing a composite value.
 *
 * All fields must be valid on creation time. Any invalid input immediately
 * raises a domain exception from the underlying typed values.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class EarlyFailTest implements ValueObjectInterface
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty $firstName,
        private FloatPositive $height,
    ) {
    }

    public static function fromArray(array $value): static
    {
        return new self(
            IntegerPositive::fromInt($value['id'] ?? 0),
            StringNonEmpty::fromString($value['firstName'] ?? ''),
            FloatPositive::fromFloat($value['height'] ?? 0.0),
        );
    }

    /**
     * Factory that validates all inputs and fails immediately on invalid data.
     *
     * @param int    $id        positive integer identifier
     * @param string $firstName non-empty person name
     * @param float  $height    positive height value
     *
     * @throws IntegerTypeException
     * @throws FloatTypeException
     * @throws StringTypeException
     */
    public static function fromScalars(
        int $id,
        string $firstName,
        float $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::fromString($firstName), // Early fail
            FloatPositive::fromFloat($height), // Early fail
        );
    }

    /**
     * Returns validated first name.
     */
    public function getFirstName(): StringNonEmpty
    {
        return $this->firstName;
    }

    /**
     * Returns validated height value.
     */
    public function getHeight(): FloatPositive
    {
        return $this->height;
    }

    /**
     * Returns validated identifier.
     */
    public function getId(): IntegerPositive
    {
        return $this->id;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function isUndefined(): bool
    {
        return false;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'firstName' => $this->firstName->value(),
            'height' => $this->height->value(),
        ];
    }
}

it('constructs EarlyFailTest from scalars and exposes typed values', function (): void {
    $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);

    expect($vo->getId()->value())->toBe(1);
    expect($vo->getFirstName()->value())->toBe('Foobar');
    expect($vo->getHeight()->value())->toBe(170.5);
});

it('fails early when id is zero or negative', function (): void {
    expect(fn() => EarlyFailTest::fromScalars(id: 0, firstName: 'Foobar', height: 10.0))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('fails early when firstName is empty', function (): void {
    expect(fn() => EarlyFailTest::fromScalars(id: 1, firstName: '', height: 10.0))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('fails early when height is negative', function (): void {
    expect(fn() => EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: -10.0))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "-10"');
});

it('returns false for isEmpty and isUndefined', function (): void {
    $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect($vo->isEmpty())->toBeFalse()
        ->and($vo->isUndefined())->toBeFalse();
});

it('converts to array', function (): void {
    $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect($vo->toArray())->toBe([
        'id' => 1,
        'firstName' => 'Foobar',
        'height' => 170.5,
    ]);
});

it('can call fromArray', function (): void {
    $data = [
        'id' => 1,
        'firstName' => 'Foobar',
        'height' => 170.5,
    ];
    $vo = EarlyFailTest::fromArray($data);
    expect($vo->getId()->value())->toBe(1)
        ->and($vo->getFirstName()->value())->toBe('Foobar')
        ->and($vo->getHeight()->value())->toBe(170.5);
});

it('fails in fromArray when id is missing (defaults to 0)', function (): void {
    expect(fn() => EarlyFailTest::fromArray(['firstName' => 'A', 'height' => 1.0]))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});

it('fails in fromArray when firstName is missing (defaults to empty)', function (): void {
    expect(fn() => EarlyFailTest::fromArray(['id' => 1, 'height' => 1.0]))
        ->toThrow(StringTypeException::class, 'Expected non-empty string, got ""');
});

it('fails in fromArray when height is missing (defaults to 0.0)', function (): void {
    expect(fn() => EarlyFailTest::fromArray(['id' => 1, 'firstName' => 'A']))
        ->toThrow(FloatTypeException::class, 'Expected positive float, got "0"');
});

it('serializes to JSON correctly', function (): void {
    $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
    expect(json_encode($vo))->toBe('{"id":1,"firstName":"Foobar","height":170.5}');
});
