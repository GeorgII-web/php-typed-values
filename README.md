PHP Typed Values
================

PHP 8.2 typed value objects for common PHP data types.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)
[![Tests](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)

## Why Use This Library?

- Make data safer through strong typing and validation.
- Improve readability with self-documenting types.
- Use tiny immutable objects as building blocks for larger value objects.
- Fit naturally into DDD (Domain-Driven Design) shared domain models.
- Work well with CQRS by expressing clear intent in commands and queries.
- Extendable with custom-typed values.

Install
-------

Using Composer:

```
composer require georgii-web/php-typed-values
```

Usage
-----

1. Use existing typed values with validation built in:

```php
$id = PositiveInt::fromString('123');
```

instead of duplicating this logic across your application like:

```php
$id = (int) '123';
if ($id <= 0) {
    throw new InvalidArgumentException('Invalid ID');
}
```

2. Create aliases:

```php
readonly class Id extends PositiveInt {}

Id::fromString('123');
```

3. Create a composite value object from other typed values (nullable values example):

```php
final class Profile
{
    public function __construct(
        public readonly PositiveInt $id,
        public readonly NonEmptyStr $firstName,
        public readonly ?NonEmptyStr $lastName,
    ) {}

    public static function fromScalars(
        int $id,
        string $firstName,
        string $lastName,
    ): self {
        return new self(
            PositiveInt::fromInt($id),
            NonEmptyStr::fromString($firstName),
            $lastName !== null ? NonEmptyStr::fromString($lastName) : null,
        );
    }
}

// Usage
Profile::fromScalars(id: 101, firstName: 'Alice', lastName: 'Smith');
Profile::fromScalars(id: 157, firstName: 'Tom', lastName: null);
```


## Key Features

- **Static analysis** – Designed for tools like Psalm and PHPStan with precise type annotations.
- **Strict types** – Uses `declare(strict_types=1);` and strict type hints throughout.
- **Validation** – Validates input on construction so objects can’t be created in an invalid state.
- **Immutable** – Value objects are read‑only and never change after creation.
- **No external dependencies** – Pure PHP implementation without requiring third‑party packages.
- **Extendable** – Extendable with custom-typed values and composite value objects.

More information
-------

See [docs/INSTALL.md](docs/INSTALL.md) for installation instructions.  
See [docs/USAGE.md](docs/USAGE.md) for usage examples.  
See [docs/DEVELOP.md](docs/DEVELOP.md) for development details.


License
-------

MIT
