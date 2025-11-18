PHP Typed Values
================

Typed value objects for common PHP data types. Make primitives explicit, safe, and self-documenting with tiny immutable value objects.

- Requires PHP 8.2+
- Zero runtime dependencies
- Tooling: Pest, PHPUnit, Psalm, PHP-CS-Fixer

Quick links:
- Install: docs/INSTALL.md
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
use GeorgiiWeb\\PhpTypedValues\\Types\\Integer\\PositiveInt;
use GeorgiiWeb\\PhpTypedValues\\Types\\Integer\\NonNegativeInt;
use GeorgiiWeb\\PhpTypedValues\\Types\\Integer\\Nullable\\PositiveIntOrNull;

$age = new PositiveInt(27);          // ok
$retries = new NonNegativeInt(0);    // ok

// Nullable wrapper: accepts PositiveInt or null semantics
$maybeCount = new PositiveIntOrNull(null);  // ok
$maybeCount = new PositiveIntOrNull(5);     // ok

// Access the underlying scalar value
$ageValue = $age->value(); // 27
```

All value objects are immutable; invalid input throws an InvalidArgumentException.

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
