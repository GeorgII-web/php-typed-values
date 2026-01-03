<?php

declare(strict_types=1);

use PhpTypedValues\Exception\PathStringTypeException;
use PhpTypedValues\String\StringPath;
use PhpTypedValues\Undefined\Alias\Undefined;

it('checks valid paths', function (): void {
    expect(StringPath::fromString('/etc/passwd')->value())->toBe('/etc/passwd')
        ->and(StringPath::fromString('etc/passwd')->value())->toBe('etc/passwd')
        ->and(StringPath::fromString('/')->value())->toBe('/')
        ->and(StringPath::fromString('etc')->value())->toBe('etc')
        ->and(StringPath::fromString('etc/')->value())->toBe('etc/')
        ->and(StringPath::fromString('/etc')->value())->toBe('/etc')
        ->and(StringPath::fromString('etc/passwd/')->value())->toBe('etc/passwd/')
        ->and(StringPath::fromString('//etc//passwd//')->value())->toBe('//etc//passwd//')
        ->and(StringPath::fromString('/etc/passwd/')->value())->toBe('/etc/passwd/');
});

it('accepts valid path, preserves value/toString and casts via __toString', function (): void {
    $p1 = new StringPath('/src/String');
    $p2 = new StringPath('src\String\\');

    expect($p1->value())
        ->toBe('/src/String')
        ->and($p1->toString())
        ->toBe('/src/String')
        ->and((string) $p1)
        ->toBe('/src/String')
        ->and($p2->value())
        ->toBe('src\String\\')
        ->and($p2->toString())
        ->toBe('src\String\\')
        ->and((string) $p2)
        ->toBe('src\String\\');
});

it('throws PathStringTypeException on empty or invalid paths', function (): void {
    expect(fn() => new StringPath(''))
        ->toThrow(PathStringTypeException::class, 'Expected non-empty path')
        ->and(fn() => StringPath::fromString('path/to/file*.txt'))
        ->toThrow(PathStringTypeException::class, 'Expected valid path, got "path/to/file*.txt"')
        ->and(fn() => StringPath::fromString('invalid?path'))
        ->toThrow(PathStringTypeException::class, 'Expected valid path, got "invalid?path"');
});

it('tryFromString returns instance for valid and Undefined for invalid', function (): void {
    $ok = StringPath::tryFromString('/etc/passwd');
    $bad = StringPath::tryFromString('bad?path');

    expect($ok)
        ->toBeInstanceOf(StringPath::class)
        ->and($ok->value())
        ->toBe('/etc/passwd')
        ->and($bad)
        ->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(StringPath::tryFromString('/var/log')->jsonSerialize())->toBeString();
});

it('tryFromMixed returns instance for valid paths and Undefined for invalid or non-convertible', function (): void {
    $fromString = StringPath::tryFromMixed('/home/user');
    $fromStringable = StringPath::tryFromMixed(new class implements Stringable {
        public function __toString(): string
        {
            return 'src\String';
        }
    });
    $fromInvalidType = StringPath::tryFromMixed([]);
    $fromInvalidValue = StringPath::tryFromMixed('path*');
    $fromNull = StringPath::tryFromMixed(null);
    $fromObject = StringPath::tryFromMixed(new stdClass());

    expect($fromString)
        ->toBeInstanceOf(StringPath::class)
        ->and($fromString->value())
        ->toBe('/home/user')
        ->and($fromStringable)
        ->toBeInstanceOf(StringPath::class)
        ->and($fromStringable->value())
        ->toBe('src\String')
        ->and($fromInvalidType)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromInvalidValue)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromNull)
        ->toBeInstanceOf(Undefined::class)
        ->and($fromObject)
        ->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringPath', function (): void {
    $p = new StringPath('/tmp');
    expect($p->isEmpty())->toBeFalse();
});

it('isUndefined is always false for StringPath', function (): void {
    $p = new StringPath('/tmp');
    expect($p->isUndefined())->toBeFalse();
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringPath::fromString('/tmp');
    expect($v->isTypeOf(StringPath::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringPath::fromString('/tmp');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringPath::fromString('/tmp');
    expect($v->isTypeOf('NonExistentClass', StringPath::class, 'AnotherClass'))->toBeTrue();
});
