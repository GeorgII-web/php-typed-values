<?php

declare(strict_types=1);

use PhpTypedValues\Exception\Integer\IntegerTypeException;
use PhpTypedValues\Float\FloatPositive;
use PhpTypedValues\Integer\IntegerPositive;
use PhpTypedValues\String\StringNonEmpty;
use PhpTypedValues\Undefined\Alias\Undefined;

/**
 * Example of late‑fail semantics using `Undefined` for optional/invalid inputs.
 *
 * - `id` must be valid at construction time (early fail).
 * - `firstName` and `height` accept mixed inputs and may become `Undefined`;
 *   accessing their string/primitive values may fail later.
 *
 * @internal
 *
 * @psalm-internal PhpTypedValues
 *
 * @psalm-immutable
 */
final readonly class LateFailTest
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {
    }

    /**
     * Factory that validates only the strictly required field (`id`) early,
     * while optional/mixed inputs for `firstName` and `height` may result in
     * `Undefined` values (late‑fail on access).
     *
     * @param int                   $id        positive integer identifier (validated immediately)
     * @param mixed                 $firstName non-empty string or will become `Undefined`
     * @param string|float|int|null $height    positive numeric or `null` to become `Undefined`
     *
     * @throws IntegerTypeException
     */
    public static function fromScalars(
        int $id,
        mixed $firstName,
        string|float|int|null $height,
    ): self {
        return new self(
            IntegerPositive::fromInt($id), // Early fail
            StringNonEmpty::tryFromMixed($firstName), // Late fail
            FloatPositive::tryFromMixed($height), // Late fail
        );
    }

    /**
     * Returns first name or `Undefined`.
     */
    public function getFirstName(): StringNonEmpty|Undefined
    {
        return $this->firstName;
    }

    /**
     * Returns height, which may be `Undefined`.
     */
    public function getHeight(): FloatPositive|Undefined
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
}

it('constructs LateFailTest from scalars/mixed and exposes typed values', function (): void {
    $vo = LateFailTest::fromScalars(id: 1, firstName: 'Foobar', height: 170);

    expect($vo->getId()->toString())->toBe('1');
    expect($vo->getFirstName()->toString())->toBe('Foobar');
    expect($vo->getHeight()->toString())->toBe('170.0');
});

it('coerces mixed valid values via tryFromMixed', function (): void {
    $vo = LateFailTest::fromScalars(id: 2, firstName: 123, height: '170.5');

    expect($vo->getId()->toString())->toBe('2');
    expect($vo->getFirstName()->toString())->toBe('123');
    expect($vo->getHeight()->toString())->toBe('170.5');
});

it('keeps Undefined for invalid optional firstName (late fail)', function (): void {
    $vo = LateFailTest::fromScalars(id: 1, firstName: '', height: 10);

    expect($vo->getFirstName())->toBeInstanceOf(Undefined::class);
    // height remains valid
    expect($vo->getHeight()->toString())->toBe('10.0');
});

it('keeps Undefined for invalid optional height values (late fail)', function (): void {
    $vo1 = LateFailTest::fromScalars(id: 1, firstName: 'Foo', height: -10);
    expect($vo1->getHeight())->toBeInstanceOf(Undefined::class);

    $vo2 = LateFailTest::fromScalars(id: 1, firstName: 'Foo', height: null);
    expect($vo2->getHeight())->toBeInstanceOf(Undefined::class);

    $vo3 = LateFailTest::fromScalars(id: 1, firstName: 'Foo', height: 'abc');
    expect($vo3->getHeight())->toBeInstanceOf(Undefined::class);
});

it('fails early on invalid id', function (): void {
    expect(fn() => LateFailTest::fromScalars(id: 0, firstName: 'Foo', height: 10))
        ->toThrow(IntegerTypeException::class, 'Expected positive integer, got "0"');
});
