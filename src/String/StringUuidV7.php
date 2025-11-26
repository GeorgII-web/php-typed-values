<?php

declare(strict_types=1);

namespace PhpTypedValues\String;

use const STR_PAD_LEFT;

use PhpTypedValues\Code\Contract\GenerateInterface;
use PhpTypedValues\Code\Exception\StringTypeException;
use PhpTypedValues\Code\String\StrType;
use Random\RandomException;

use function ord;
use function preg_match;
use function sprintf;
use function strtolower;

/**
 * UUID version 7 (time-ordered, Unix time-based).
 *
 * @psalm-immutable
 */
readonly class StringUuidV7 extends StrType implements GenerateInterface
{
    /** @var non-empty-string */
    protected string $value;

    /**
     * @throws StringTypeException
     */
    public function __construct(string $value)
    {
        // Normalize to lowercase for consistent representation
        $normalized = strtolower($value);

        if ($normalized === '') {
            // Distinct message for empty input
            throw new StringTypeException(sprintf('Expected non-empty UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        // RFC 4122-style UUID v7:
        // xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx (hex, case-insensitive)
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $normalized) !== 1) {
            throw new StringTypeException(sprintf('Expected UUID v7 (xxxxxxxx-xxxx-7xxx-[89ab]xxx-xxxxxxxxxxxx), got "%s"', $value));
        }

        $this->value = $normalized;
    }

    /**
     * @throws StringTypeException
     */
    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /** @return non-empty-string */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throws RandomException
     * @throws StringTypeException
     */
    public static function generate(): static
    {
        // Get current Unix time in milliseconds (48 bits)
        // Use a float literal for the multiplier to satisfy Psalm strict operands (float*float)
        $timeMs = (int) floor(microtime(true) * 1000.0);

        // Split timestamp into 48 bits: high 32 bits and low 16 bits
        $timeHigh = ($timeMs & 0xFFFFFFFF0000) >> 16; // upper 32 bits of 48-bit timestamp
        $timeLow = $timeMs & 0xFFFF;                 // lower 16 bits

        // Generate 10 random bytes for the remaining fields
        $random = random_bytes(10);

        // Build the UUID fields (all integers) according to UUID v7 layout
        // time_high (32 bits)
        $timeHighHex = str_pad(dechex($timeHigh), 8, '0', STR_PAD_LEFT);

        // time_mid (16 bits)
        $timeMidHex = str_pad(dechex($timeLow), 4, '0', STR_PAD_LEFT);

        // time_hi_and_version (16 bits): high 4 bits = version 7 (0b0111)
        $timeHiAndVersion = (ord($random[0]) & 0x0F) | 0x70; // clear high 4 bits, set to 0111
        $timeHiAndVersionHex = sprintf('%02x%02x', $timeHiAndVersion, ord($random[1]));

        // clock_seq_hi_and_reserved (8 bits): set variant to 0b10xx
        $clockSeqHi = (ord($random[2]) & 0x3F) | 0x80; // clear top 2 bits, set to 10
        $clockSeqLow = ord($random[3]);
        $clockSeqHex = sprintf('%02x%02x', $clockSeqHi, $clockSeqLow);

        // node (48 bits) = remaining 6 random bytes
        $nodeHex = bin2hex(substr($random, 4, 6));

        // Assemble canonical UUID string: 8-4-4-4-12
        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            $timeHighHex,
            $timeMidHex,
            $timeHiAndVersionHex,
            $clockSeqHex,
            $nodeHex
        );

        return new static($uuid);
    }
}
