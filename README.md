# PHP Typed Values

PHP library of typed value objects for common PHP data types.

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
$id = IntegerPositive::fromString('123');
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
readonly class Id extends IntegerPositive {}

Id::fromInt(123);
```

#### 3. Create a composite value object:

```php
final readonly class Profile
{
    public function __construct(
        public readonly IntegerPositive $id,
        public readonly StringNonEmpty $firstName,
        public readonly ?FloatNonNegative $height,
    ) {}

    public static function fromScalars(
        int $id,
        string $firstName,
        string|float|int|null $height,
    ): static {
        return new static(
            IntegerPositive::fromInt($id),
            StringNonEmpty::fromString($firstName), // Early fail
            $height !== null ? FloatNonNegative::fromString((string) $height) : null,
        );
    }
    
    public function getHeight(): FloatNonNegative|Undefined { // avoid using NULL, which could mean anything
        return $this->height ?? Undefined::create(); // Late fail
    }
}

// Usage
\PhpTypedValues\Usage\Composite::fromScalars(id: 101, firstName: 'Alice', height: '172.5');
\PhpTypedValues\Usage\Composite::fromScalars(id: 157, firstName: 'Tom', height: null);
// From array
$profile = \PhpTypedValues\Usage\Composite::fromScalars(...[157, 'Tom', null]);
// Accessing values
$profile->getHeight(); // "172.5" OR "Undefined" type class (will throw an exception on trying to get value)
```

## Key Features

- **Static analysis** – Designed for tools like Psalm and PHPStan with precise type annotations.
- **Strict types** – Uses `declare(strict_types=1);` and strict type hints throughout.
- **Validation** – Validates input on construction so objects can’t be created in an invalid state.
- **Immutable** – Value objects are read‑only and never change after creation.
- **No external dependencies** – Pure PHP implementation without requiring third‑party packages.
- **Extendable** – Extendable with custom-typed values and composite value objects.

## Performance disclaimer

- **Performance** for an array of objects is about `3x` **slower** than an array of scalars;
- **Memory usage** for an array of objects is about `2x` **higher**;
- **Use value objects** for domain modeling, type safety, and validation boundaries;
- **Use raw scalars** for high-performance loops or large-scale data processing;

## More information

See [docs/USAGE.md](docs/USAGE.md) for usage examples.  
See [docs/DEVELOP.md](docs/DEVELOP.md) for development details.

## License

MIT
