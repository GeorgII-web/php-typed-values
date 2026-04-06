<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeCookie;
use PhpTypedValues\Exception\DateTime\CookieDateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DateTimeCookie::class);

describe('DateTimeCookie', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same instant and toString is COOKIE', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = DateTimeCookie::fromDateTime($dt);

                expect($vo->toString())->toBe('Thursday, 02-Jan-2025 03:04:05 UTC');
            });
        });

        describe('fromString', function () {
            it('parses valid COOKIE and preserves timezone offset (normalized to UTC)', function () {
                $vo = DateTimeCookie::fromString('Tuesday, 31-Dec-2030 23:59:59 GMT');

                expect($vo->toString())->toBe('Tuesday, 31-Dec-2030 23:59:59 UTC');
            });

            it('throws exception on invalid input', function (string $input) {
                expect(fn() => DateTimeCookie::fromString($input))
                    ->toThrow(CookieDateTimeTypeException::class);
            })->with([
                'invalid month' => ['Thursday, 02-Bad-2025 03:04:05 UTC'],
                'wrong day name' => ['Monday, 02-Jan-2025 03:04:05 UTC'], // 2025-01-02 is Thursday
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = DateTimeCookie::tryFromMixed($input);
                expect($result)->toBeInstanceOf(DateTimeCookie::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['Thursday, 02-Jan-2025 03:04:05 UTC', 'Thursday, 02-Jan-2025 03:04:05 UTC'],
                'DateTimeImmutable instance' => [new DateTimeImmutable('2025-01-02 03:04:05 UTC'), 'Thursday, 02-Jan-2025 03:04:05 UTC'],
                'Stringable object' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return 'Thursday, 02-Jan-2025 03:04:05 UTC';
                        }
                    },
                    'Thursday, 02-Jan-2025 03:04:05 UTC',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = DateTimeCookie::tryFromMixed($input);
                expect($result)->toBeInstanceOf(Undefined::class);
            })->with([
                'null' => [null],
                'array' => [['x']],
                'stdClass' => [new stdClass()],
                'anonymous non-stringable' => [new class {}],
            ]);
        });

        describe('tryFromString', function () {
            it('returns instance or default value', function (string $input, bool $isSuccess) {
                $result = DateTimeCookie::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(DateTimeCookie::class);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['Thursday, 02-Jan-2025 03:04:05 UTC', true],
                'invalid' => ['bad', false],
            ]);
        });

        describe('getFormat', function () {
            it('returns COOKIE format', function () {
                expect(DateTimeCookie::getFormat())->toBe(\DATE_COOKIE);
            });
        });
    });

    describe('Instance Methods', function () {
        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(DateTimeCookie::class)
                    ->and($vo2->toString())->toBe('Thursday, 02-Jan-2025 03:04:05 UTC');
            });
        });

        it('toString returns formatted string', function () {
            $s = 'Thursday, 02-Jan-2025 03:04:05 UTC';
            $vo = DateTimeCookie::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
            expect($vo->isEmpty())->toBeFalse();
        });

        it('isUndefined is always false', function () {
            $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
            expect($vo->isUndefined())->toBeFalse();
        });

        it('jsonSerialize returns string', function () {
            $s = 'Thursday, 02-Jan-2025 03:04:05 UTC';
            expect(DateTimeCookie::fromString($s)->jsonSerialize())->toBe($s);
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                expect($vo->isTypeOf(DateTimeCookie::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                expect($vo->isTypeOf('NonExistentClass', DateTimeCookie::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = DateTimeCookie::fromString('Thursday, 02-Jan-2025 03:04:05 UTC');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
