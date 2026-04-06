<?php

declare(strict_types=1);

use PhpTypedValues\DateTime\DateTimeRFC1123;
use PhpTypedValues\Exception\DateTime\RFC1123DateTimeTypeException;
use PhpTypedValues\Undefined\Alias\Undefined;

covers(DateTimeRFC1123::class);

describe('DateTimeRFC1123', function () {
    describe('Creation', function () {
        describe('fromDateTime', function () {
            it('returns same instant and toString is RFC1123', function () {
                $dt = new DateTimeImmutable('2025-01-02T03:04:05+00:00');
                $vo = DateTimeRFC1123::fromDateTime($dt);

                expect($vo->toString())->toBe('Thu, 02 Jan 2025 03:04:05 +0000');
            });
        });

        describe('fromString', function () {
            it('parses valid RFC1123', function () {
                $vo = DateTimeRFC1123::fromString('Tue, 31 Dec 2030 23:59:59 +0000');
                expect($vo->toString())->toBe('Tue, 31 Dec 2030 23:59:59 +0000');
            });

            it('throws exception on invalid input', function (string $input) {
                expect(fn() => DateTimeRFC1123::fromString($input))
                    ->toThrow(RFC1123DateTimeTypeException::class);
            })->with([
                'invalid format' => ['2025-01-02 03:04:05'],
                'wrong day' => ['Mon, 02 Jan 2025 03:04:05 +0000'],
            ]);
        });

        describe('tryFromMixed', function () {
            it('returns instance for valid mixed inputs', function (mixed $input, string $expected) {
                $result = DateTimeRFC1123::tryFromMixed($input);
                expect($result)->toBeInstanceOf(DateTimeRFC1123::class)
                    ->and($result->toString())->toBe($expected);
            })->with([
                'valid string' => ['Thu, 02 Jan 2025 03:04:05 +0000', 'Thu, 02 Jan 2025 03:04:05 +0000'],
                'DateTimeImmutable instance' => [new DateTimeImmutable('2025-01-02 03:04:05 +0000'), 'Thu, 02 Jan 2025 03:04:05 +0000'],
                'Stringable object' => [
                    new class implements Stringable {
                        public function __toString(): string
                        {
                            return 'Thu, 02 Jan 2025 03:04:05 +0000';
                        }
                    },
                    'Thu, 02 Jan 2025 03:04:05 +0000',
                ],
            ]);

            it('returns Undefined for invalid mixed inputs', function (mixed $input) {
                $result = DateTimeRFC1123::tryFromMixed($input);
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
                $result = DateTimeRFC1123::tryFromString($input);
                if ($isSuccess) {
                    expect($result)->toBeInstanceOf(DateTimeRFC1123::class);
                } else {
                    expect($result)->toBeInstanceOf(Undefined::class);
                }
            })->with([
                'valid' => ['Thu, 02 Jan 2025 03:04:05 +0000', true],
                'invalid' => ['bad', false],
            ]);
        });

        describe('getFormat', function () {
            it('returns RFC1123 format', function () {
                expect(DateTimeRFC1123::getFormat())->toBe(\DATE_RFC1123);
            });
        });
    });

    describe('Instance Methods', function () {
        it('jsonSerialize returns string', function () {
            $s = 'Thu, 02 Jan 2025 03:04:05 +0000';
            expect(DateTimeRFC1123::fromString($s)->jsonSerialize())->toBe($s);
        });

        describe('withTimeZone', function () {
            it('returns a new instance with updated timezone (normalized to UTC)', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                $vo2 = $vo->withTimeZone('Europe/Berlin');

                expect($vo2)->toBeInstanceOf(DateTimeRFC1123::class)
                    ->and($vo2->toString())->toBe('Thu, 02 Jan 2025 03:04:05 +0000');
            });
        });
        it('toString returns formatted string', function () {
            $s = 'Thu, 02 Jan 2025 03:04:05 +0000';
            $vo = DateTimeRFC1123::fromString($s);
            expect($vo->toString())->toBe($s)
                ->and((string) $vo)->toBe($s);
        });

        it('isEmpty is always false', function () {
            $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
            expect($vo->isEmpty())->toBeFalse();
        });

        it('isUndefined is always false', function () {
            $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
            expect($vo->isUndefined())->toBeFalse();
        });

        describe('isTypeOf', function () {
            it('returns true when class matches', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                expect($vo->isTypeOf(DateTimeRFC1123::class))->toBeTrue();
            });

            it('returns false when class does not match', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                expect($vo->isTypeOf('NonExistentClass'))->toBeFalse();
            });

            it('returns true for multiple classNames when one matches', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                expect($vo->isTypeOf('NonExistentClass', DateTimeRFC1123::class, 'AnotherClass'))->toBeTrue();
            });

            it('returns false for multiple classNames when none match', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                expect($vo->isTypeOf('NonExistentClass', 'AnotherClass'))->toBeFalse();
            });

            it('returns false for empty classNames', function () {
                $vo = DateTimeRFC1123::fromString('Thu, 02 Jan 2025 03:04:05 +0000');
                expect($vo->isTypeOf())->toBeFalse();
            });
        });
    });
});
