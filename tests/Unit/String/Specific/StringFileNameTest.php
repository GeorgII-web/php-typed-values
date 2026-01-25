<?php

declare(strict_types=1);

use PhpTypedValues\Exception\String\FileNameStringTypeException;
use PhpTypedValues\String\Specific\StringFileName;
use PhpTypedValues\Undefined\Alias\Undefined;

describe('StringFileName', function () {
    it('accepts valid filename, preserves value/toString and casts via __toString', function (): void {
        $f = new StringFileName('image.jpg');

        expect($f->value())
            ->toBe('image.jpg')
            ->and($f->toString())
            ->toBe('image.jpg')
            ->and((string) $f)
            ->toBe('image.jpg');
    });

    it('throws FileNameStringTypeException on empty or invalid filenames', function (): void {
        expect(fn() => new StringFileName(''))
            ->toThrow(FileNameStringTypeException::class, 'Expected non-empty file name')
            ->and(fn() => StringFileName::fromString('path/to/file.txt'))
            ->toThrow(FileNameStringTypeException::class, 'Expected valid file name, got "path/to/file.txt"')
            ->and(fn() => StringFileName::fromString('file*.txt'))
            ->toThrow(FileNameStringTypeException::class, 'Expected valid file name, got "file*.txt"');
    });

    it('correctly extracts filename only and extension', function (): void {
        $f1 = new StringFileName('archive.tar.gz');
        $f2 = new StringFileName('no_extension');
        $f3 = new StringFileName('.hidden');

        expect($f1->getFileNameOnly())->toBe('archive.tar')
            ->and($f1->getExtension())->toBe('gz')
            ->and($f2->getFileNameOnly())->toBe('no_extension')
            ->and($f2->getExtension())->toBe('')
            ->and($f3->getFileNameOnly())->toBe('')
            ->and($f3->getExtension())->toBe('hidden');
    });

    it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
        $ok = StringFileName::tryFromString('valid.txt');
        $bad = StringFileName::tryFromString('bad/path.txt');

        expect($ok)
            ->toBeInstanceOf(StringFileName::class)
            ->and($ok->value())
            ->toBe('valid.txt')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('jsonSerialize returns string', function (): void {
        expect(StringFileName::tryFromString('test.jpg')->jsonSerialize())->toBeString();
    });

    it('tryFromMixed returns instance for valid filenames and Undefined for invalid or non-convertible', function (
    ): void {
        $fromString = StringFileName::tryFromMixed('image.png');
        $fromStringable = StringFileName::tryFromMixed(new class {
            public function __toString(): string
            {
                return 'document.pdf';
            }
        });
        $fromInvalidType = StringFileName::tryFromMixed([]);
        $fromInvalidValue = StringFileName::tryFromMixed('invalid/path.txt');
        $fromNull = StringFileName::tryFromMixed(null);
        $fromObject = StringFileName::tryFromMixed(new stdClass());

        expect($fromString)
            ->toBeInstanceOf(StringFileName::class)
            ->and($fromString->value())
            ->toBe('image.png')
            ->and($fromStringable)
            ->toBeInstanceOf(StringFileName::class)
            ->and($fromStringable->value())
            ->toBe('document.pdf')
            ->and($fromInvalidType)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromInvalidValue)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromNull)
            ->toBeInstanceOf(Undefined::class)
            ->and($fromObject)
            ->toBeInstanceOf(Undefined::class);
    });

    it('isEmpty is always false for StringFileName', function (): void {
        $f = new StringFileName('file.txt');
        expect($f->isEmpty())->toBeFalse();
    });

    it('isUndefined is always false for StringFileName', function (): void {
        $f = new StringFileName('file.txt');
        expect($f->isUndefined())->toBeFalse();
    });

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringFileName::fromString('test.txt');
        expect($v->isTypeOf(StringFileName::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringFileName::fromString('test.txt');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
    });

    it('isTypeOf returns true for multiple classNames when one matches', function (): void {
        $v = StringFileName::fromString('test.txt');
        expect($v->isTypeOf('NonExistentClass', StringFileName::class, 'AnotherClass'))->toBeTrue();
    });

    it('covers conversions for StringFileName', function (): void {
        expect(StringFileName::fromBool(true)->value())->toBe('true')
            ->and(StringFileName::fromFloat(1.2)->value())->toBe('1.19999999999999996')
            ->and(StringFileName::fromInt(123)->value())->toBe('123');

        $v = StringFileName::fromString('image.jpg');
        expect(fn() => $v->toBool())->toThrow(PhpTypedValues\Exception\Integer\IntegerTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(PhpTypedValues\Exception\String\StringTypeException::class);
    });

    it('tryFromBool, tryFromFloat, tryFromInt return StringFileName for valid inputs', function (): void {
        expect(StringFileName::tryFromBool(true))->toBeInstanceOf(StringFileName::class)
            ->and(StringFileName::tryFromFloat(1.2))->toBeInstanceOf(StringFileName::class)
            ->and(StringFileName::tryFromInt(123))->toBeInstanceOf(StringFileName::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringFileNameTest extends StringFileName
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromFloat(float $value): static
    {
        throw new Exception('test');
    }

    public static function fromInt(int $value): static
    {
        throw new Exception('test');
    }

    public static function fromString(string $value): static
    {
        throw new Exception('test');
    }
}

describe('Throwing static', function () {
    it('StringFileName::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringFileNameTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringFileNameTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringFileNameTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringFileNameTest::tryFromMixed('image.jpg'))->toBeInstanceOf(Undefined::class)
            ->and(StringFileNameTest::tryFromString('image.jpg'))->toBeInstanceOf(Undefined::class);
    });
});
