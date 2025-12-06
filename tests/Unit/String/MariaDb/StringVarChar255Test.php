<?php

declare(strict_types=1);

use PhpTypedValues\Exception\StringTypeException;
use PhpTypedValues\String\MariaDb\StringVarChar255;

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
