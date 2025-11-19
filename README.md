PHP Typed Values
================

Typed value objects for common PHP data types. Make primitives explicit, safe, and self-documenting with tiny immutable value objects.

- Requires PHP 8.2+
- Zero runtime dependencies
- Tooling: Pest, PHPUnit, Psalm, PHP-CS-Fixer

Quick links:
- Install: docs/INSTALL.md
- Usage: docs/USAGE.md
- Development: docs/DEVELOP.md

Install
-------

Using Composer:

```
composer require georgii-web/php-typed-values
```

Usage
-----

Create and use typed integers with validation built in.

```php
use PhpTypedValues\\Integer\\PositiveInt;
use PhpTypedValues\\Integer\\NonNegativeInt;
use PhpTypedValues\\Integer\\WeekDayInt;
use PhpTypedValues\\Integer\\Integer;

$age = new PositiveInt(27);          // ok (positive-int)
$retries = new NonNegativeInt(0);    // ok (0 or positive)
$weekday = new WeekDayInt(5);        // ok (1..7)
$any = new Integer(-42);             // ok (any integer)

// Construct from string
$fromString = PositiveInt::fromString('123');

// Access the underlying scalar value
$ageValue = $age->value(); // 27
echo $weekday->toString(); // "5"
```

All value objects are immutable; invalid input throws an exception with a helpful message.

Provided integer types (so far):

- Integer — any PHP integer
- PositiveInt — positive integer (> 0)
- NonNegativeInt — zero or positive integer (>= 0)
- WeekDayInt — integer in range 1..7

Why
---

- Replace loose primitives with explicit, intention-revealing types
- Centralize validation in one place
- Improve static analysis and readability

Scripts
-------

Helpful Composer scripts are included:

- composer test - run tests (Pest)
- composer type - 100% type coverage gate
- composer coverage - code coverage gate
- composer sca - static analysis (Psalm)
- composer cs - coding standards (PHP-CS-Fixer)

See docs/DEVELOP.md for details.

License
-------

MIT
