<?php

declare(strict_types=1);

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\String\MariaDb\StringVarChar255;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts empty string and preserves value', function (): void {
    $s = new StringVarChar255('');
    expect($s->value())->toBe('')
        ->and($s->toString())->toBe('');
});

it('accepts 255 ASCII characters (boundary) and preserves value', function (): void {
    $str = str_repeat('a', 255);
    $s = StringVarChar255::fromString($str);
    expect($s->value())->toBe($str)
        ->and($s->toString())->toBe($str);
});

it('throws on 256 ASCII characters (above boundary)', function (): void {
    $str = str_repeat('b', 256);
    expect(fn() => new StringVarChar255($str))
        ->toThrow(StringTypeException::class, 'String is too long, max 255 chars allowed');
});

it('accepts 255 multibyte characters (emoji) counted by mb_strlen', function (): void {
    $str = str_repeat('🙂', 255);
    $s = new StringVarChar255($str);
    expect($s->value())->toBe($str)
        ->and($s->toString())->toBe($str);
});

it('throws on 256 multibyte characters (emoji)', function (): void {
    $str = str_repeat('🙂', 256);
    expect(fn() => StringVarChar255::fromString($str))
        ->toThrow(StringTypeException::class, 'String is too long, max 255 chars allowed');
});

it('StringVarChar255::tryFromString returns value when length <= 255', function (): void {
    $short = str_repeat('a', 255);
    $v = StringVarChar255::tryFromString($short);

    expect($v)
        ->toBeInstanceOf(StringVarChar255::class)
        ->and($v->value())
        ->toBe($short);
});

it('StringVarChar255::tryFromString returns Undefined when length > 255', function (): void {
    $long = str_repeat('b', 256);
    $u = StringVarChar255::tryFromString($long);

    expect($u)->toBeInstanceOf(Undefined::class);
});

it('jsonSerialize returns string', function (): void {
    expect(StringVarChar255::tryFromString('hello')->jsonSerialize())->toBeString();
});

it('__toString returns the original string value', function (): void {
    $str = str_repeat('C', 10);
    $s = new StringVarChar255($str);

    expect((string) $s)->toBe($str)
        ->and($s->__toString())->toBe($str);
});

it('tryFromMixed handles valid/too-long strings, stringable, and invalid mixed inputs', function (): void {
    // valid within limit
    $ok = StringVarChar255::tryFromMixed(str_repeat('a', 255));

    // stringable within limit
    $val = str_repeat('b', 5);
    $stringable = new class($val) {
        public function __construct(private string $v)
        {
        }

        public function __toString(): string
        {
            return $this->v;
        }
    };
    $fromStringable = StringVarChar255::tryFromMixed($stringable);

    // too long
    $tooLong = StringVarChar255::tryFromMixed(str_repeat('x', 256));

    // invalid mixed
    $fromArray = StringVarChar255::tryFromMixed(['x']);
    $fromNull = StringVarChar255::tryFromMixed(null);

    expect($ok)->toBeInstanceOf(StringVarChar255::class)
        ->and($ok->value())->toBe(str_repeat('a', 255))
        ->and($fromStringable)->toBeInstanceOf(StringVarChar255::class)
        ->and($fromStringable->value())->toBe($val)
        ->and($tooLong)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        // null converts to empty string which is valid for VarChar(255)
        ->and($fromNull)->toBeInstanceOf(StringVarChar255::class)
        ->and($fromNull->value())->toBe('');
});
