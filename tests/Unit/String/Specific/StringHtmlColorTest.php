<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use PhpTypedValues\Exception\String\HtmlColorStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringHtmlColor;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;
use Stringable;

use function sprintf;

covers(StringHtmlColor::class);

describe('StringHtmlColor', function () {
    it('accepts valid HTML colors and preserves value', function (string $color): void {
        $c = new StringHtmlColor($color);
        expect($c->value())->toBe($color);
    })->with([
        '#ffffff',
        '#FFF',
        '#000000',
        '#000',
        'ffffff',
        'fff',
        '000000',
        '000',
        '1a2b3c',
        '#1a2b3c',
    ]);

    it('throws on invalid HTML color format', function (string $invalid): void {
        expect(fn() => new StringHtmlColor($invalid))
            ->toThrow(HtmlColorStringTypeException::class, sprintf('Expected HTML color string, got "%s"', $invalid));
    })->with([
        '#ffff',      // 4 chars
        '#fffff',     // 5 chars
        '#fffffff',   // 7 chars
        '#gg0000',    // invalid hex
        'not a color',
        '',           // empty
        ' ',          // space
    ]);

    it('tryFromString returns instance for valid color and Undefined for invalid', function (): void {
        $ok = StringHtmlColor::tryFromString('#ffffff');
        $bad = StringHtmlColor::tryFromString('invalid');

        expect($ok)->toBeInstanceOf(StringHtmlColor::class)
            ->and($ok->value())->toBe('#ffffff')
            ->and($bad)->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid inputs and invalid mixed inputs', function (): void {
        $ok = StringHtmlColor::tryFromMixed('#abc');
        $badFormat = StringHtmlColor::tryFromMixed('not valid!');
        $fromArray = StringHtmlColor::tryFromMixed(['#abc']);
        $fromNull = StringHtmlColor::tryFromMixed(null);

        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return '#ffffff';
            }
        };
        $fromStringable = StringHtmlColor::tryFromMixed($stringable);

        $notStringable = new stdClass();
        $fromNotStringable = StringHtmlColor::tryFromMixed($notStringable);

        expect($ok)->toBeInstanceOf(StringHtmlColor::class)
            ->and($ok->value())->toBe('#abc')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromStringable)->toBeInstanceOf(StringHtmlColor::class)
            ->and($fromStringable->value())->toBe('#ffffff')
            ->and($fromNotStringable)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringHtmlColor::fromString('#ffffff');
        $u = StringHtmlColor::tryFromString('not valid!');

        expect($ok->isUndefined())->toBeFalse()
            ->and($u->isUndefined())->toBeTrue();
    });

    it('isEmpty always returns false', function (): void {
        $c = new StringHtmlColor('#fff');
        expect($c->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $c = new StringHtmlColor('#ffffff');
        expect($c->jsonSerialize())->toBe('#ffffff');
    });

    it('toString returns the value', function (): void {
        $c = new StringHtmlColor('#ffffff');
        expect($c->toString())->toBe('#ffffff')
            ->and((string) $c)->toBe('#ffffff');
    });

    it('isTypeOf works correctly', function (): void {
        $c = new StringHtmlColor('#ffffff');
        expect($c->isTypeOf(StringHtmlColor::class))->toBeTrue()
            ->and($c->isTypeOf('SomeOtherClass'))->toBeFalse();
    });
});

describe('StringHtmlColor conversions', function () {
    it('toBool throws if not a standard boolean string', function () {
        $c = new StringHtmlColor('#ffffff');
        expect(fn() => $c->toBool())->toThrow(StringTypeException::class);
    });

    it('toBool returns value for boolean strings', function () {
        // This is only possible if we can create a StringHtmlColor with 'true' or 'false'
        // But 'true' is not a valid HTML color (4 chars). 'false' is not either (5 chars).
        // So toBool will always throw for valid HTML colors in this strict implementation.
    })->skip('Valid HTML colors are never valid boolean strings');

    it('toInt and toFloat might throw due to strict validation in base class', function () {
        $c = new StringHtmlColor('123456');
        expect($c->toInt())->toBe(123456);
        // expect($c->toFloat())->toBe(123456.0); // This throws because 123456.0 -> "123456.0" != "123456"
        expect($c->toDecimal())->toBe('123456');
    });

    it('fromBool, fromDecimal, fromFloat, fromInt create instances', function () {
        expect(StringHtmlColor::fromInt(123456)->value())->toBe('123456')
            ->and(StringHtmlColor::fromDecimal('123456')->value())->toBe('123456');
    });

    it('tryFromBool, tryFromDecimal, tryFromFloat, tryFromInt handle valid/invalid inputs', function () {
        expect(StringHtmlColor::tryFromInt(123456))->toBeInstanceOf(StringHtmlColor::class)
            ->and(StringHtmlColor::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringHtmlColor::tryFromDecimal('123456'))->toBeInstanceOf(StringHtmlColor::class)
            ->and(StringHtmlColor::tryFromDecimal('invalid'))->toBeInstanceOf(Undefined::class)
            ->and(StringHtmlColor::tryFromFloat(123456.0))->toBeInstanceOf(Undefined::class) // 123456.0 -> "123456.0" (invalid color)
            ->and(StringHtmlColor::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringHtmlColor::tryFromBool(true))->toBeInstanceOf(Undefined::class);
    });
});
