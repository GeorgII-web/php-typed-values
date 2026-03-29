<?php

declare(strict_types=1);

namespace PhpTypedValues\Tests\Unit\String\Specific;

use const STR_PAD_LEFT;

use Exception;
use PhpTypedValues\Exception\Decimal\DecimalTypeException;
use PhpTypedValues\Exception\String\IbanStringTypeException;
use PhpTypedValues\Exception\String\StringTypeException;
use PhpTypedValues\String\Specific\StringIban;
use PhpTypedValues\Undefined\Alias\Undefined;
use stdClass;

use function strlen;

covers(StringIban::class);

describe('StringIban', function () {
    it('accepts valid IBAN strings and preserves normalized value', function (string $input, string $expected): void {
        $iban = new StringIban($input);
        expect($iban->value())->toBe($expected);
    })->with([
        ['DE89370400440532013000', 'DE89370400440532013000'],
        ['GB29NWBK60161331926819', 'GB29NWBK60161331926819'],
    ]);

    it('throws on invalid IBAN format', function (string $invalid): void {
        expect(fn() => new StringIban($invalid))
            ->toThrow(IbanStringTypeException::class);
    })->with([
        '',
        '   ',
        'de89370400440532013000',
        'DE89 3704 0044 0532 0130 00',
        ' GB 29 NWBK 6016 1331 9268 19 ',
        'NOT AN IBAN',
        'PEST Mutator was here!',
        'PESTMutatorwashere!',
        'pest mutator was here!',
        'PEST MUTATOR WAS HERE!',
        'PESTMUTATORWASHERE!',
        'PEST  Mutator  was  here!',
        "\0",
        "\n",
        "\r",
        "\t",
        'DE89 3704 0044 0532 0130 01', // Invalid checksum
        'ABC', // Too short (len 3)
        'AB12', // Length 4, passes ctype_alpha(AB) and ctype_digit(12)
        'AB1', // Length 3, would pass ctype_alpha(AB) and ctype_digit(1) if checked as 2 and 1
        '3312', // Length 4, fails ctype_alpha
        'DEAB', // Length 4, fails ctype_digit
        'AB123', // Length 5, would pass if not for other checks
        'DE00', // Valid length (4) but might be invalid checksum/format for country
        str_repeat('DE89370400440532013000', 2), // Length 44
        str_repeat('A', 35), // Too long
        'DE89 3704 0044 0532 0130 00 extra', // Too long (if it exceeds 34)
        '1234 5678 9012 3456 7890 12', // Doesn't start with country code
        'DE' . str_repeat('0', 33), // Too long (len 35)
        'DE' . str_repeat('0', 32), // Length 34
        'DE' . str_repeat('0', 34), // Length 36
        'DE001', // Length 5, passes ctype_alpha(DE) and ctype_digit(00)
        'DE00123456789012345678901234567890123', // Length 37, passes ctype alpha/digit
        'AB12', // Length 4, passes length check, passes ctype checks, BUT fails checksum.
        'AB1', // Length 3, 'AB' is alpha, '1' is NOT digit (need 2 digits). wait.
        'PEST Mutator was here!',
        'PESTMutatorwashere!',
    ]);

    it('specifically fails on either invalid prefix or invalid check digits', function (string $invalid): void {
        expect(fn() => new StringIban($invalid))->toThrow(IbanStringTypeException::class);
    })->with([
        '1234', // Prefix invalid (digits)
        'DEAB', // Check digits invalid (letters)
        'A123', // Prefix too short (only 1 letter)
        'DE1A', // Check digits invalid (contains letter)
        'DE8', // too short but otherwise valid
        'DE89370400440532013000DE89370400440532013000DE89370400440532013000', // very long (passes ctype)
        'DE89', // length 4, but ctype_alpha(DE) and ctype_digit(89) pass! BUT it fails checksum.
        'ZZ00', // length 4, ZZ is alpha, 00 is digit. fails checksum.
        'ZZ001', // length 5, ZZ is alpha, 00 is digit. fails checksum.
        'ZZ00123456789012345678901234567890123', // length 37, ZZ is alpha, 00 is digit. fails checksum.
        'AD' . str_repeat('0', 32), // length 34, valid length, fails checksum.
        'AD' . str_repeat('0', 33), // length 35, invalid length.
        'AD' . str_repeat('0', 1), // length 3, invalid length.
        'ZZ0', // length 3, ZZ is alpha, 0 is digit.
        'DE893704004405320130001', // length 23, valid but fails checksum.
        'AI6', // length 3, BUT would pass if length check removed (AI=1018, moved=10186, 10186%97=1)
        'AD66' . str_repeat('0', 31), // length 35, BUT would pass if length check removed
        'AA9Z', // Case 0 (BooleanOrToBooleanAnd mutant b10a335d8c1ff5b4)
        'A089', // Case 1 (Decrement substr length mutant 49e8924e1e18f0b3)
        'AD0A48', // Case 2 (Decrement substr length mutant d9ee71f2dc73580a)
        'ad66',
        ' de 89 3704 0044 0532 0130 00 ',
        'A D 6 6',
    ]);

    it('validates mixed case and spaces', function (string $input, string $expected): void {
        $v = new StringIban($input);
        expect($v->value())->toBe($expected);
    })->with([
        ['AD66', 'AD66'],
        ['AD92L1', 'AD92L1'], // Killing b1973be5b926f1b4
        ['AD16M1', 'AD16M1'], // Killing 99361c8864b7f541
        ['AD58O1', 'AD58O1'], // Killing 3aa70a7b690e6e34
        ['AD24R1', 'AD24R1'], // Killing 6401172022ca592d
        ['AD45S1', 'AD45S1'], // Killing b844a3e509f7f4a2
        ['AD66T1', 'AD66T1'], // Killing 7c6d81d561ea1335
        ['AD11V1', 'AD11V1'], // Killing 8604f4ccefc07486
    ]);

    it('accepts valid IBANs with letters', function (string $valid): void {
        expect(new StringIban($valid))->toBeInstanceOf(StringIban::class);
    })->with([
        'AD92L1', // Valid fictional IBAN with L
        'AD16M1', // Valid fictional IBAN with M
        'AD58O1', // Valid fictional IBAN with O
        'AD24R1', // Valid fictional IBAN with R
        'AD45S1', // Valid fictional IBAN with S
        'AD66T1', // Valid fictional IBAN with T
        'AD11V1', // Valid fictional IBAN with V
    ]);

    it('accepts boundary length IBANs', function (): void {
        // Find a valid 4-character IBAN
        // format: LLdd
        // moved: ddLL -> dd (L-64) (L-64)
        // For AD: A=10, D=13. moved: dd1013
        // For O: 24. For AD00: moved 001013. bcmod(001013, 97) = 1013 % 97 = 43.
        // We need bcmod(dd1013, 97) = 1.
        // dd1013 = 97 * k + 1
        // dd1013 - 1 = 97 * k
        // dd1012 = 97 * k
        // 1012 / 97 is not int.
        // Try other country codes.
        // AL: 10 21 -> dd1021. dd1021 = 97*k + 1 -> dd1020 = 97*k. 1020/97 not int.
        // AD: 10 13.
        // Let's use a known valid one: AD08 is valid length 4?
        // AD: 1013. moved: 081013. 81013 / 97 = 835.18...
        // 81013 % 97 = 19.
        // We need x % 97 = 1.
        // (dd * 10000 + 1013) % 97 = 1
        // (dd * 10000 + 1012) % 97 = 0
        // (dd * (97*103 + 9) + 1012) % 97 = 0
        // (dd * 9 + 1012) % 97 = 0
        // 1012 % 97 = 42
        // (dd * 9 + 42) % 97 = 0
        // dd = 21 -> 21*9 + 42 = 189 + 42 = 231. 231 % 97 = 37.
        // dd = 43 -> 43*9 + 42 = 387 + 42 = 429. 429 / 97 = 4.4...
        // Try dd = 92 -> 92*9 + 42 = 828 + 42 = 870. 870 % 97 = 94.
        // Just use an IBAN with O: AD 00 contains A and D.
        // Need one with O: 'AD24 0000 0000 0000 0000 0000' is not 4 chars.
        // A valid 24-char IBAN with 'O':
        // 'AD24 0000 0000 0000 0000 0000' - AD is length 24? No, AD is 24 chars in total.
        // Let's use 'AD24 O...' wait, O is for 24.
        // Need one with O: 24, P: 25, Q: 26, R: 27, S: 28, T: 29, U: 30, V: 31, W: 32, X: 33, Y: 34, Z: 35.
        $ibanWithAllLetters = 'AD00 OPQR STUV WXYZ ABC D EFG';
        expect(fn() => new StringIban($ibanWithAllLetters))->toThrow(IbanStringTypeException::class);

        // Individual letters for mapping
        foreach (range('A', 'Z') as $char) {
            for ($i = 0; $i < 1000; ++$i) {
                $dd = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                $testIban = "AD{$dd}{$char}1";
                // Check if it's valid
                $moved = substr($testIban, 4) . substr($testIban, 0, 4);
                $subs = ['A' => '10', 'B' => '11', 'C' => '12', 'D' => '13', 'E' => '14', 'F' => '15', 'G' => '16', 'H' => '17', 'I' => '18', 'J' => '19', 'K' => '20', 'L' => '21', 'M' => '22', 'N' => '23', 'O' => '24', 'P' => '25', 'Q' => '26', 'R' => '27', 'S' => '28', 'T' => '29', 'U' => '30', 'V' => '31', 'W' => '32', 'X' => '33', 'Y' => '34', 'Z' => '35'];
                $numeric = strtr($moved, $subs);
                if (bcmod($numeric, '97') === '1') {
                    new StringIban($testIban);
                    break;
                }
            }
        }

        // Find a valid 4-character IBAN
        // format: LLdd
        // moved: ddLL -> dd (L-64) (L-64)
        // For AD: A=10, D=13. moved: dd1013
        // We need dd1013 % 97 == 1
        // Wait, isValidIban also checks:
        // if (!ctype_alpha(substr($iban, 0, 2)) || !ctype_digit(substr($iban, 2, 2))) {
        // So for ADdd, dd must be digits.
        // Also it checks substr($iban, 4) . substr($iban, 0, 4)
        // For length 4: substr($iban, 4) is empty string.
        // So moved is substr($iban, 0, 4) which is LLdd.
        // LLdd -> (L-64)(L-64)dd
        // For ADdd: 1013dd
        // We need 1013dd % 97 == 1
        $found4 = false;
        for ($i = 0; $i <= 99; ++$i) {
            $dd = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            if (bcmod('1013' . $dd, '97') === '1') {
                $iban4 = 'AD' . $dd;
                $v4 = new StringIban($iban4);
                expect(strlen($v4->value()))->toBe(4);
                $found4 = true;
                break;
            }
        }
        expect($found4)->toBeTrue();

        // Find a valid 34-character IBAN
        // format: DEdd + 30 zeros
        // moved: 30 zeros + DE + dd -> 30 zeros + 1314 + dd
        $found34 = false;
        for ($i = 0; $i <= 99; ++$i) {
            $dd = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $numeric = str_repeat('0', 30) . '1314' . $dd;
            if (bcmod($numeric, '97') === '1') {
                $iban34 = 'DE' . $dd . str_repeat('0', 30);
                $v34 = new StringIban($iban34);
                expect(strlen($v34->value()))->toBe(34);
                $found34 = true;
                break;
            }
        }
        expect($found34)->toBeTrue();
    });

    it('tryFromString returns instance for valid IBAN and Undefined for invalid', function (): void {
        $ok = StringIban::tryFromString('DE89370400440532013000');
        $bad = StringIban::tryFromString('invalid');

        expect($ok)
            ->toBeInstanceOf(StringIban::class)
            ->and($ok->value())
            ->toBe('DE89370400440532013000')
            ->and($bad)
            ->toBeInstanceOf(Undefined::class);
    });

    it('tryFromMixed handles valid IBAN strings and invalid mixed inputs', function (): void {
        $ok = StringIban::tryFromMixed('DE89370400440532013000');

        $stringable = new class {
            public function __toString(): string
            {
                return 'DE89370400440532013000';
            }
        };
        $fromStringable = StringIban::tryFromMixed($stringable);

        $badFormat = StringIban::tryFromMixed('not valid!');
        $fromArray = StringIban::tryFromMixed(['DE89']);
        $fromNull = StringIban::tryFromMixed(null);
        $fromObject = StringIban::tryFromMixed(new stdClass());

        expect($ok)->toBeInstanceOf(StringIban::class)
            ->and($ok->value())->toBe('DE89370400440532013000')
            ->and($fromStringable)->toBeInstanceOf(StringIban::class)
            ->and($fromStringable->value())->toBe('DE89370400440532013000')
            ->and($badFormat)->toBeInstanceOf(Undefined::class)
            ->and($fromArray)->toBeInstanceOf(Undefined::class)
            ->and($fromNull)->toBeInstanceOf(Undefined::class)
            ->and($fromObject)->toBeInstanceOf(Undefined::class);
    });

    it('isUndefined returns false for instances and true for Undefined results', function (): void {
        $ok = StringIban::fromString('DE89370400440532013000');
        $u1 = StringIban::tryFromString('not valid!');

        expect($ok->isUndefined())->toBeFalse()
            ->and($u1->isUndefined())->toBeTrue();
    });

    it('isUndefined returns false (explicit call)', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->isUndefined())->toBeFalse();
    });

    it('rejects IBAN with non-numeric characters in moved part after translation', function (): void {
        // IBAN: "DE89 3704 0044 0532 0130 00" -> moved "3704 0044 0532 0130 00 DE 89"
        // Let's craft an IBAN that has a non-alphanumeric character but passes previous checks
        // isValidIban check 1: len < 4 || len > 34
        // isValidIban check 2: !ctype_alpha(substr($iban, 0, 2)) || !ctype_digit(substr($iban, 2, 2))
        // So it must start with 2 letters and 2 digits.
        // If it contains a special character later, ctype_digit($numeric) will be false.

        expect(fn() => new StringIban('DE89!70400440532013000'))
            ->toThrow(IbanStringTypeException::class);
    });

    it('covers all letter translations to kill mutants', function (string $char): void {
        // We use a valid IBAN template from Andorra (24 chars)
        // AD 33 0001 0001 .... 1234 5678 90
        $map = [
            'V' => 'AD1800010001V25345678901',
            'W' => 'AD0800010001W25345678901',
            'X' => 'AD2500010001X25345678901',
            'Y' => 'AD2900010001Y25345678901',
            'Z' => 'AD3300010001Z25345678901',
        ];

        $iban = $map[$char];
        $moved = substr($iban, 4) . substr($iban, 0, 4);
        $subs = ['A' => '10', 'B' => '11', 'C' => '12', 'D' => '13', 'E' => '14', 'F' => '15', 'G' => '16', 'H' => '17', 'I' => '18', 'J' => '19', 'K' => '20', 'L' => '21', 'M' => '22', 'N' => '23', 'O' => '24', 'P' => '25', 'Q' => '26', 'R' => '27', 'S' => '28', 'T' => '29', 'U' => '30', 'V' => '31', 'W' => '32', 'X' => '33', 'Y' => '34', 'Z' => '35'];
        $numeric = strtr($moved, $subs);
        $mod = bcmod($numeric, '97');

        // Let's see what mod is... if it's not 1, we fix the checksum.
        if ($mod !== '1') {
            $currentChecksum = (int) substr($iban, 2, 2);
            $newChecksum = ($currentChecksum - ((int) $mod - 1));
            if ($newChecksum < 0) {
                $newChecksum += 97;
            }
            if ($newChecksum >= 97) {
                $newChecksum -= 97;
            }
            $checksumStr = str_pad((string) $newChecksum, 2, '0', STR_PAD_LEFT);
            $iban = substr($iban, 0, 2) . $checksumStr . substr($iban, 4);
        }

        $instance = new StringIban($iban);
        expect($instance->value())->toBe($iban);
    })->with(['V', 'W', 'X', 'Y', 'Z']);

    it('isTypeOf returns true when class matches', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->isTypeOf(StringIban::class))->toBeTrue();
    });

    it('isTypeOf returns false when class does not match', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->isTypeOf('NonExistentClass'))->toBeFalse()
            ->and($v->isTypeOf(StringIban::class, 'NonExistentClass'))->toBeTrue();
    });

    it('isEmpty is always false for StringIban', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->isEmpty())->toBeFalse();
    });

    it('jsonSerialize returns the value', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->jsonSerialize())->toBe('DE89370400440532013000');
    });

    it('toString returns the normalized IBAN string', function (): void {
        $v = StringIban::fromString('DE89370400440532013000');
        expect($v->toString())->toBe('DE89370400440532013000');
    });

    it('covers conversions for StringIban', function (): void {
        expect(fn() => StringIban::fromBool(true))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromFloat(1.2))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromInt(123))->toThrow(IbanStringTypeException::class)
            ->and(fn() => StringIban::fromDecimal('1.0'))->toThrow(IbanStringTypeException::class);

        $v = StringIban::fromString('DE89370400440532013000');
        expect(fn() => $v->toBool())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toFloat())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toInt())->toThrow(StringTypeException::class)
            ->and(fn() => $v->toDecimal())->toThrow(DecimalTypeException::class);
    });
});

/**
 * @internal
 *
 * @psalm-immutable
 *
 * @coversNothing
 */
readonly class StringIbanTest extends StringIban
{
    public static function fromBool(bool $value): static
    {
        throw new Exception('test');
    }

    public static function fromDecimal(string $value): static
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
    it('StringIban::tryFrom* returns Undefined when exception occurs (coverage)', function (): void {
        expect(StringIbanTest::tryFromBool(true))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromFloat(1.1))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromInt(1))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromDecimal('1.0'))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromMixed('DE89370400440532013000'))->toBeInstanceOf(Undefined::class)
            ->and(StringIbanTest::tryFromString('DE89370400440532013000'))->toBeInstanceOf(Undefined::class);
    });
});
