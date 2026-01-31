### PHP Typed Values

Typed value objects for PHP. Build precise, immutable, and validated data for DTOs, Value Objects, and Entities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)
[![Tests](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)

---

### Install

- Use V3 for PHP 8.4:

```bash
composer require georgii-web/php-typed-values:^3
```

- Use V2 for PHP >=8.2 & <8.4:

```bash
composer require georgii-web/php-typed-values:^2
```

- Use V1 for PHP 7.4:

```bash
composer require georgii-web/php-typed-values:^1
```

### Why

- Strong typing for scalars with runtime validation
- Immutable and self‑documenting values
- Safer constructors for your DTOs/VOs/Entities
- Great fit for static analysis
- Safe type conversion, no silent errors

### Quick start

#### Use existing typed values

```php
use PhpTypedValues\Integer\IntegerPositive;

$id = IntegerPositive::fromString('123');
```

Instead of spreading validation across an application

```php
$id = (int) '123';
if ($id <= 0) {
    throw new InvalidArgumentException('Invalid ID');
}
```

#### Create an alias (domain name)

```php
use PhpTypedValues\Integer\IntegerPositive;

readonly class Id extends IntegerPositive {}

Id::fromInt(123);
```

Other usage examples [docs/USAGE.md](docs/USAGE.md)

### Key features

- Idempotent conversion on fromString() > toString(): "1" > 1 > "1"
- Static analysis friendly
- Strict types
- Validation on construction; no invalid state
- Immutable, readonly objects
- No external runtime dependencies
- Easy to extend with your own types and composites
- Heavily tested 

### Performance note

- Objects vs Scalars:
    - ~2.3× slower for large arrays of objects
    - ~1.5× higher memory usage
- Use value objects for domain boundaries, validation, and clarity
- Use raw scalars in hot loops or large data processing paths

### Documentation

- Development guide: [docs/DEVELOP.md](docs/DEVELOP.md)
- More usage examples in [tests/Unit](tests/Unit)

### License

MIT