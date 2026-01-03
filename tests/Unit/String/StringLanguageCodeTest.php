<?php

declare(strict_types=1);

use PhpTypedValues\Exception\LanguageCodeStringTypeException;
use PhpTypedValues\String\StringLanguageCode;
use PhpTypedValues\Undefined\Alias\Undefined;

it('accepts valid language code and preserves toString and __toString', function (): void {
    $l = new StringLanguageCode('en');

    expect($l->value())
        ->toBe('en')
        ->and($l->toString())
        ->toBe('en')
        ->and((string) $l)
        ->toBe('en');
});

it('throws on malformed or unknown language codes', function (): void {
    // Wrong length/format
    expect(fn() => new StringLanguageCode('e'))
        ->toThrow(LanguageCodeStringTypeException::class, 'Expected ISO 639-1 language code (aa), got "e"')
        ->and(fn() => StringLanguageCode::fromString('123'))
        ->toThrow(LanguageCodeStringTypeException::class, 'Expected ISO 639-1 language code (aa), got "123"');

    // Looks like a code, but not in our allow-list
    expect(fn() => StringLanguageCode::fromString('zz'))
        ->toThrow(LanguageCodeStringTypeException::class, 'Unknown ISO 639-1 language code "zz"');
});

it('throws on uppercase language codes', function (): void {
    expect(fn() => StringLanguageCode::fromString('EN'))
        ->toThrow(LanguageCodeStringTypeException::class, 'Expected ISO 639-1 language code (aa), got "EN"')
        ->and(fn() => StringLanguageCode::fromString('De'))
        ->toThrow(LanguageCodeStringTypeException::class, 'Expected ISO 639-1 language code (aa), got "De"');
});

it('tryFromString returns instance for valid code and Undefined for invalid/unknown', function (): void {
    $ok = StringLanguageCode::tryFromString('de');
    $bad1 = StringLanguageCode::tryFromString('e');
    $bad2 = StringLanguageCode::tryFromString('zz');
    $bad3 = StringLanguageCode::tryFromString('EN');

    expect($ok)
        ->toBeInstanceOf(StringLanguageCode::class)
        ->and($ok->value())
        ->toBe('de')
        ->and($bad1)
        ->toBeInstanceOf(Undefined::class)
        ->and($bad2)
        ->toBeInstanceOf(Undefined::class)
        ->and($bad3)
        ->toBeInstanceOf(Undefined::class);
});

it('checks every language code', function (): void {
    /** @var list<non-empty-string> $codes */
    $codes = [
        'aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av', 'ay', 'az',
        'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs',
        'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv', 'cy',
        'da', 'de', 'dv', 'dz',
        'ee', 'el', 'en', 'eo', 'es', 'et', 'eu',
        'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy',
        'ga', 'gd', 'gl', 'gn', 'gu', 'gv',
        'ha', 'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy', 'hz',
        'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu',
        'ja', 'jv',
        'ka', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky',
        'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv',
        'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt', 'my',
        'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny',
        'oc', 'oj', 'om', 'or', 'os',
        'pa', 'pi', 'pl', 'ps', 'pt',
        'qu',
        'rm', 'rn', 'ro', 'ru', 'rw',
        'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw',
        'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty',
        'ug', 'uk', 'ur', 'uz',
        've', 'vi', 'vo',
        'wa', 'wo',
        'xh',
        'yi', 'yo',
        'za', 'zh', 'zu',
    ];

    foreach ($codes as $code) {
        expect(StringLanguageCode::fromString($code)->value())->toBe($code);
    }
});

it('explicitly accepts tail-list language codes yi, yo, za, zh, zu (guards against element removal mutations)', function (): void {
    expect(StringLanguageCode::fromString('yi')->value())->toBe('yi')
        ->and(StringLanguageCode::fromString('yo')->value())->toBe('yo')
        ->and(StringLanguageCode::fromString('za')->value())->toBe('za')
        ->and(StringLanguageCode::fromString('zh')->value())->toBe('zh')
        ->and(StringLanguageCode::fromString('zu')->value())->toBe('zu');
});

it('accepts common language codes', function (string $code): void {
    $lang = StringLanguageCode::fromString($code);
    expect($lang->value())->toBe($code);
})->with([
    'en', 'de', 'fr', 'es', 'it', 'pt', 'ru', 'zh', 'ja', 'ko',
    'ar', 'hi', 'nl', 'sv', 'da', 'no', 'fi', 'pl', 'tr', 'cs',
]);

it('jsonSerialize returns string', function (): void {
    expect(StringLanguageCode::fromString('de')->jsonSerialize())->toBeString()->toBe('de');
});

it('tryFromMixed handles valid language codes and invalid mixed inputs', function (): void {
    // valid lowercase string
    $ok = StringLanguageCode::tryFromMixed('en');

    // stringable producing a valid code
    $stringable = new class {
        public function __toString(): string
        {
            return 'de';
        }
    };
    $fromStringable = StringLanguageCode::tryFromMixed($stringable);

    // invalid: uppercase format, unknown code, wrong types
    $badUpper = StringLanguageCode::tryFromMixed('EN');
    $badUnknown = StringLanguageCode::tryFromMixed('zz');
    $fromArray = StringLanguageCode::tryFromMixed(['en']);
    $fromNull = StringLanguageCode::tryFromMixed(null);
    $fromScalar = StringLanguageCode::tryFromMixed(123); // invalid code but covers scalar check
    $fromObject = StringLanguageCode::tryFromMixed(new stdClass());

    expect($ok)->toBeInstanceOf(StringLanguageCode::class)
        ->and($ok->value())->toBe('en')
        ->and($fromStringable)->toBeInstanceOf(StringLanguageCode::class)
        ->and($fromStringable->value())->toBe('de')
        ->and($badUpper)->toBeInstanceOf(Undefined::class)
        ->and($badUnknown)->toBeInstanceOf(Undefined::class)
        ->and($fromArray)->toBeInstanceOf(Undefined::class)
        ->and($fromNull)->toBeInstanceOf(Undefined::class)
        ->and($fromScalar)->toBeInstanceOf(Undefined::class)
        ->and($fromObject)->toBeInstanceOf(Undefined::class);
});

it('isEmpty is always false for StringLanguageCode', function (): void {
    $l = new StringLanguageCode('en');
    expect($l->isEmpty())->toBeFalse();
});

it('isUndefined returns false for instances and true for Undefined results', function (): void {
    // Valid instance
    $ok = StringLanguageCode::fromString('en');

    // Invalid via tryFrom*: wrong case, unknown code, and non-string mixed
    $u1 = StringLanguageCode::tryFromString('EN');
    $u2 = StringLanguageCode::tryFromString('zz');
    $u3 = StringLanguageCode::tryFromMixed(['en']);

    expect($ok->isUndefined())->toBeFalse()
        ->and($u1->isUndefined())->toBeTrue()
        ->and($u2->isUndefined())->toBeTrue()
        ->and($u3->isUndefined())->toBeTrue();
});

it('round-trip conversion preserves value: string → object → string', function (): void {
    $original = 'fr';
    $lang = StringLanguageCode::fromString($original);
    $str = $lang->toString();

    expect($str)->toBe($original);
});

it('value method returns the same as toString', function (): void {
    $lang = new StringLanguageCode('es');
    expect($lang->value())->toBe($lang->toString());
});

it('__toString magic method works correctly', function (): void {
    $lang = new StringLanguageCode('ja');
    expect((string) $lang)->toBe('ja')
        ->and($lang . ' language')->toBe('ja language');
});

it('isTypeOf returns true when class matches', function (): void {
    $v = StringLanguageCode::fromString('en');
    expect($v->isTypeOf(StringLanguageCode::class))->toBeTrue();
});

it('isTypeOf returns false when class does not match', function (): void {
    $v = StringLanguageCode::fromString('en');
    expect($v->isTypeOf('NonExistentClass'))->toBeFalse();
});

it('isTypeOf returns true for multiple classNames when one matches', function (): void {
    $v = StringLanguageCode::fromString('en');
    expect($v->isTypeOf('NonExistentClass', StringLanguageCode::class, 'AnotherClass'))->toBeTrue();
});
