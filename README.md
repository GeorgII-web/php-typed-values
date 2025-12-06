# PHP Typed Values

A PHP 7.4 || 8.2 library of typed value objects for common PHP data types.

Building blocks for a DTO's, ValueObjects, Entities, etc.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)
[![Tests](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)

## Install

Use `v2.*` for PHP 8.2 support:

```
composer require georgii-web/php-typed-values:^2.0
```

Use `v1.*` for PHP 7.4 support:

```
composer require georgii-web/php-typed-values:^1.0
```

## Usage

#### 1. Use existing typed values with validation built in:

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

#### 2. Create aliases:

```php
readonly class Id extends PositiveInt {}

Id::fromInt(123);
```

#### 3. Create a composite value object:

```php
final class Profile
{
    public function __construct(
        public readonly PositiveInt $id,
        public readonly NonEmptyStr $firstName,
        public readonly ?FloatNonNegative $height,
    ) {}

    public static function fromScalars(
        int $id,
        string $firstName,
        string|float|int|null $height,
    ): static {
        return new static(
            PositiveInt::fromInt($id),
            NonEmptyStr::fromString($firstName),
            $height !== null ? FloatNonNegative::fromString((string) $height) : null,
        );
    }
    
    public function getHeight(): FloatNonNegative|Undefined { // avoid using NULL, which could mean anything
        return $this->height ?? Undefined::create();
    }
}

// Usage
Profile::fromScalars(id: 101, firstName: 'Alice', height: '172.5');
Profile::fromScalars(id: 157, firstName: 'Tom', height: null);
// From array
$profile = Profile::fromScalars(...[157, 'Tom', null]);
// Accessing values
$profile->getHeight(); // "172.5 \ Undefined" type class
```

## Key Features

- **Static analysis** – Designed for tools like Psalm and PHPStan with precise type annotations.
- **Strict types** – Uses `declare(strict_types=1);` and strict type hints throughout.
- **Validation** – Validates input on construction so objects can’t be created in an invalid state.
- **Immutable** – Value objects are read‑only and never change after creation.
- **No external dependencies** – Pure PHP implementation without requiring third‑party packages.
- **Extendable** – Extendable with custom-typed values and composite value objects.

## More information

See [docs/USAGE.md](docs/USAGE.md) for usage examples.  
See [docs/DEVELOP.md](docs/DEVELOP.md) for development details.

## License

MIT
