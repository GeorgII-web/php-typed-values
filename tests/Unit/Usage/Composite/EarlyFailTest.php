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
 * Composite of strict early‑fail semantics for constructing a composite value.
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

describe('EarlyFailTest', function () {
    describe('Creation', function () {
        it('constructs from scalars and exposes typed values', function (): void {
            $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);

            expect($vo->getId()->value())->toBe(1)
                ->and($vo->getFirstName()->value())->toBe('Foobar')
                ->and($vo->getHeight()->value())->toBe(170.5);
        });

        it('fails early on invalid scalar inputs', function (int $id, string $firstName, float $height, string $exception, string $message) {
            expect(fn() => EarlyFailTest::fromScalars(id: $id, firstName: $firstName, height: $height))
                ->toThrow($exception, $message);
        })->with([
            'id zero' => [0, 'Foobar', 10.0, IntegerTypeException::class, 'Expected positive integer, got "0"'],
            'id negative' => [-1, 'Foobar', 10.0, IntegerTypeException::class, 'Expected positive integer, got "-1"'],
            'firstName empty' => [1, '', 10.0, StringTypeException::class, 'Expected non-empty string, got ""'],
            'height negative' => [1, 'Foobar', -10.0, FloatTypeException::class, 'Expected positive float, got "-10"'],
            'height zero' => [1, 'Foobar', 0.0, FloatTypeException::class, 'Expected positive float, got "0"'],
        ]);

        describe('fromArray', function () {
            it('constructs from valid array', function (): void {
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

            it('fails when fields are missing (triggers defaults that fail validation)', function (array $data, string $exception, string $message) {
                expect(fn() => EarlyFailTest::fromArray($data))
                    ->toThrow($exception, $message);
            })->with([
                'id missing' => [['firstName' => 'A', 'height' => 1.0], IntegerTypeException::class, 'Expected positive integer, got "0"'],
                'firstName missing' => [['id' => 1, 'height' => 1.0], StringTypeException::class, 'Expected non-empty string, got ""'],
                'height missing' => [['id' => 1, 'firstName' => 'A'], FloatTypeException::class, 'Expected positive float, got "0"'],
            ]);
        });
    });

    describe('State and Accessors', function () {
        it('returns false for isEmpty and isUndefined', function (): void {
            $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
            expect($vo->isEmpty())->toBeFalse()
                ->and($vo->isUndefined())->toBeFalse();
        });

        it('exposes internal typed values via getters', function () {
            $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
            expect($vo->getId())->toBeInstanceOf(IntegerPositive::class)
                ->and($vo->getFirstName())->toBeInstanceOf(StringNonEmpty::class)
                ->and($vo->getHeight())->toBeInstanceOf(FloatPositive::class);
        });
    });

    describe('Serialization', function () {
        it('converts to array', function (): void {
            $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
            expect($vo->toArray())->toBe([
                'id' => 1,
                'firstName' => 'Foobar',
                'height' => 170.5,
            ]);
        });

        it('serializes to JSON correctly', function (): void {
            $vo = EarlyFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170.5);
            expect(json_encode($vo))->toBe('{"id":1,"firstName":"Foobar","height":170.5}');
        });
    });
});
