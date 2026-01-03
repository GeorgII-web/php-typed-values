<?php

declare(strict_types=1);

namespace Tests\Unit\ArrayType;

use JsonSerializable;
use PhpTypedValues\ArrayType\ArrayNonEmpty;
use PhpTypedValues\Exception\ArrayTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Stringable;

it('throws ArrayTypeException when constructed with an empty array', function (): void {
    new ArrayNonEmpty([]);
})->throws(ArrayTypeException::class, 'Expected non-empty array');

it('accepts a non-empty array of objects', function (): void {
    $items = [new stdClass(), new stdClass()];
    $vo = new ArrayNonEmpty($items);

    expect($vo->value())->toBe($items)
        ->and($vo->count())->toBe(2)
        ->and($vo->isEmpty())->toBeFalse();
});

it('can be created fromArray', function (): void {
    $items = [new stdClass()];
    $vo = ArrayNonEmpty::fromArray($items);

    expect($vo)->toBeInstanceOf(ArrayNonEmpty::class)
        ->and($vo->value())->toBe($items);
});

it('can be created tryFromArray', function (): void {
    $items = [new stdClass()];
    $vo = ArrayNonEmpty::tryFromArray($items);

    expect($vo)->toBeInstanceOf(ArrayNonEmpty::class)
        ->and($vo->value())->toBe($items);
});

it('implements IteratorAggregate via getIterator', function (): void {
    $items = [new stdClass(), new stdClass()];
    $vo = new ArrayNonEmpty($items);

    $collected = [];
    foreach ($vo as $item) {
        $collected[] = $item;
    }

    expect($collected)->toBe($items);
});

it('toArray returns the serialized array', function (): void {
    $item = new class implements JsonSerializable {
        public function jsonSerialize(): string
        {
            return 'serialized';
        }
    };
    $vo = new ArrayNonEmpty([$item]);

    expect($vo->toArray())->toBe(['serialized']);
});

it('jsonSerialize returns the serialized array', function (): void {
    $item = new class implements JsonSerializable {
        public function jsonSerialize(): string
        {
            return 'serialized';
        }
    };
    $vo = new ArrayNonEmpty([$item]);

    expect($vo->jsonSerialize())->toBe(['serialized']);
});

it('toArray handles scalars and null', function (): void {
    $items = ['string', 123, 1.23, true, null];
    // We need to bypass the @template TItem of object for this test if we want to test scalars
    // but the class is not strictly enforcing objects at runtime in the constructor,
    // though the docblock says list<TItem>.
    // Let's see if the constructor allows them.
    /** @var array<any> $items */
    $vo = new ArrayNonEmpty($items);

    expect($vo->toArray())->toBe($items);
});

it('toArray handles Stringable objects', function (): void {
    $item = new class implements Stringable {
        public function __toString(): string
        {
            return 'stringable';
        }
    };
    $vo = new ArrayNonEmpty([$item]);

    expect($vo->toArray())->toBe(['stringable']);
});

it('toArray throws ArrayTypeException for unsupported types', function (): void {
    $item = new stdClass();
    $vo = new ArrayNonEmpty([$item]);

    $vo->toArray();
})->throws(ArrayTypeException::class, 'Item of type "stdClass" cannot be converted to a scalar or JSON-serializable value.');

it('toArray continues processing multiple JsonSerializable items', function (): void {
    $item1 = new class implements JsonSerializable {
        public function jsonSerialize(): string
        {
            return 'item1';
        }
    };
    $item2 = new class implements JsonSerializable {
        public function jsonSerialize(): string
        {
            return 'item2';
        }
    };
    $vo = new ArrayNonEmpty([$item1, $item2]);

    expect($vo->toArray())->toBe(['item1', 'item2']);
});

it('toArray continues processing multiple Stringable items', function (): void {
    $item1 = new class implements Stringable {
        public function __toString(): string
        {
            return 'item1';
        }
    };
    $item2 = new class implements Stringable {
        public function __toString(): string
        {
            return 'item2';
        }
    };
    $vo = new ArrayNonEmpty([$item1, $item2]);

    expect($vo->toArray())->toBe(['item1', 'item2']);
});

it('toArray continues processing mixed items', function (): void {
    $json = new class implements JsonSerializable {
        public function jsonSerialize(): string
        {
            return 'json';
        }
    };
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'stringable';
        }
    };
    $items = [$json, 'scalar', $stringable, null];
    /** @var array<any> $items */
    $vo = new ArrayNonEmpty($items);

    expect($vo->toArray())->toBe(['json', 'scalar', 'stringable', null]);
});

it('isUndefined returns true only if all items are Undefined', function (): void {
    $u1 = Undefined::create();
    $u2 = Undefined::create();
    $o1 = new stdClass();

    $allUndefined = new ArrayNonEmpty([$u1, $u2]);
    $mixed = new ArrayNonEmpty([$u1, $o1]);
    $noneUndefined = new ArrayNonEmpty([$o1]);

    expect($allUndefined->isUndefined())->toBeTrue()
        ->and($mixed->isUndefined())->toBeFalse()
        ->and($noneUndefined->isUndefined())->toBeFalse();
});

it('hasUndefined returns true if any item is Undefined', function (): void {
    $u1 = Undefined::create();
    $o1 = new stdClass();

    $mixed = new ArrayNonEmpty([$u1, $o1]);
    $noneUndefined = new ArrayNonEmpty([$o1]);

    expect($mixed->hasUndefined())->toBeTrue()
        ->and($noneUndefined->hasUndefined())->toBeFalse();
});

it('getDefinedItems filters out Undefined instances', function (): void {
    $u1 = Undefined::create();
    $o1 = new stdClass();
    $o2 = new stdClass();

    $vo = new ArrayNonEmpty([$u1, $o1, $u1, $o2]);

    expect($vo->getDefinedItems())->toBe([$o1, $o2]);
});

it('isUndefined returns false if empty (unreachable via constructor)', function (): void {
    $vo = (new ReflectionClass(ArrayNonEmpty::class))->newInstanceWithoutConstructor();

    // Set 'value' property to [] using reflection since it's uninitialized
    $reflectionProperty = new ReflectionProperty(ArrayNonEmpty::class, 'value');
    $reflectionProperty->setValue($vo, []);

    expect($vo->isUndefined())->toBeFalse();
});

it('isEmpty returns false even for a single element array', function (): void {
    $vo = new ArrayNonEmpty([new stdClass()]);
    expect($vo->isEmpty())->toBeFalse();
});

it('isEmpty returns true if empty (unreachable via constructor)', function (): void {
    $vo = (new ReflectionClass(ArrayNonEmpty::class))->newInstanceWithoutConstructor();
    $reflectionProperty = new ReflectionProperty(ArrayNonEmpty::class, 'value');
    $reflectionProperty->setValue($vo, []);

    expect($vo->isEmpty())->toBeTrue();
});

it('isTypeOf returns true when class matches', function (): void {
    $vo = new ArrayNonEmpty([new stdClass()]);
    expect($vo->isTypeOf(ArrayNonEmpty::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $vo = new ArrayNonEmpty([new stdClass()]);
    expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $vo = new ArrayNonEmpty([new stdClass()]);
    expect($vo->isTypeOf('NonExistentClass', ArrayNonEmpty::class))->toBeTrue();
});
