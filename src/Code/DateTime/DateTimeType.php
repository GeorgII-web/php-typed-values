<?php

declare(strict_types=1);

namespace PhpTypedValues\Code\DateTime;

use const DATE_ATOM;

use DateTimeImmutable;
use Exception;
use PhpTypedValues\Code\Exception\DateTimeTypeException;

/**
 * @psalm-immutable
 */
abstract readonly class DateTimeType implements DateTimeTypeInterface
{
    /**
     * @throws DateTimeTypeException
     */
    protected static function parseString(string $value): DateTimeImmutable
    {
        // Accept a small, explicit set of formats to avoid accidentally parsing invalid strings as "now".
        $formats = [
            DATE_ATOM,            // e.g., 2025-01-02T03:04:05+00:00
            'Y-m-d\TH:i:s\Z',    // e.g., 2025-01-02T03:04:05Z
            'Y-m-d H:i:s',        // e.g., 2025-01-02 03:04:05
            '!Y-m-d',             // e.g., 2025-01-02 (force midnight by resetting unspecified fields)
        ];

        foreach ($formats as $format) {
            $dt = DateTimeImmutable::createFromFormat($format, $value);
            if ($dt instanceof DateTimeImmutable) {
                $errors = DateTimeImmutable::getLastErrors();
                $hasIssues = $errors !== false && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);
                if (!$hasIssues) {
                    return $dt;
                }
            }
        }

        // Fallback: try the engine parser for strict ISO-8601 only using DateTimeImmutable constructor
        try {
            $fallback = new DateTimeImmutable($value);
        } catch (Exception) {
            $fallback = null;
        }

        if ($fallback instanceof DateTimeImmutable) {
            // Heuristic: if input clearly looks like an ISO 8601 (contains 'T' and either 'Z' or timezone offset), accept it
            if (preg_match('/T\d{2}:\d{2}:\d{2}(?:Z|[+\-]\d{2}:?\d{2})$/', $value) === 1) {
                return $fallback;
            }
        }

        throw new DateTimeTypeException('String has no valid datetime');
    }

    public function toString(): string
    {
        // ISO 8601 string representation
        return $this->value()->format(DATE_ATOM);
    }
}
