<?php

declare(strict_types=1);

use PhpTypedValues\Base\Primitive\PrimitiveType;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(PrimitiveType::class);

/**
 * Mock concrete implementation for testing abstract class.
 *
 * @internal
 *
 * @covers \PhpTypedValues\Base\Primitive\PrimitiveType
 */
readonly class PrimitiveTypeTest extends PrimitiveType
{
    public function __construct(private mixed $value)
    {
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function isUndefined(): bool
    {
        return $this->value instanceof Undefined;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}

it('PrimitiveType is abstract and cannot be instantiated', function () {
    expect(PrimitiveType::class)
        ->toBeAbstract()
        ->and(class_exists(PrimitiveTypeTest::class))
        ->toBeTrue();
});

describe('Concrete PrimitiveType implementation', function () {
    beforeEach(function () {
        $this->primitive = new PrimitiveTypeTest('test value');
    });

    it('isEmpty method works correctly', function ($value, $expected) {
        $primitive = new PrimitiveTypeTest($value);

        expect($primitive->isEmpty())->toBe($expected);
    })->with([
        ['value' => '', 'expected' => true],
        ['value' => 'test', 'expected' => false],
        ['value' => 0, 'expected' => true],
        ['value' => 1, 'expected' => false],
        ['value' => [], 'expected' => true],
        ['value' => null, 'expected' => true],
    ]);

    it('isUndefined method identifies Undefined instances', function () {
        $undefined = new PrimitiveTypeTest(new Undefined());
        $defined = new PrimitiveTypeTest('some value');

        expect($undefined->isUndefined())->toBeTrue()
            ->and($defined->isUndefined())->toBeFalse();
    });

    it('toString method returns string representation', function ($value, $expected) {
        $primitive = new PrimitiveTypeTest($value);

        expect($primitive->toString())->toBe($expected)
            ->and((string) $primitive)->toBe($expected);
    })->with([
        ['value' => 'test', 'expected' => 'test'],
        ['value' => 123, 'expected' => '123'],
        ['value' => 3.14, 'expected' => '3.14'],
        ['value' => true, 'expected' => '1'],
        ['value' => false, 'expected' => ''],
        ['value' => null, 'expected' => ''],
    ]);

    it('__toString magic method works correctly', function () {
        $primitive = new PrimitiveTypeTest('magic string');

        expect((string) $primitive)->toBe('magic string')
            ->and($primitive->__toString())->toBe('magic string');
    });

    it('jsonSerialize returns value for JSON encoding', function () {
        $data = ['key' => 'value'];
        $primitive = new PrimitiveTypeTest($data);

        expect($primitive->jsonSerialize())->toBe($data)
            ->and(json_encode($primitive))->toBe(json_encode($data));
    });

    it('Undefined type works correctly', function () {
        $undefined = new Undefined();

        expect($undefined->isEmpty())->toBeTrue()
            ->and($undefined->isUndefined())->toBeTrue();
    });
});

describe('Equality and comparison', function () {
    it('Different instances with same value should not be equal', function () {
        $primitive1 = new PrimitiveTypeTest('test');
        $primitive2 = new PrimitiveTypeTest('test');

        expect($primitive1)->not->toBe($primitive2)
            ->and($primitive1->toString())->toBe($primitive2->toString());
    });

    it('String casting works in concatenation', function () {
        $primitive = new PrimitiveTypeTest('world');
        $result = 'Hello ' . $primitive;

        expect($result)->toBe('Hello world');
    });
});
