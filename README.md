### PHP Typed Values

Typed value objects for PHP. Build precise, immutable, and validated data for DTOs, Value Objects, and Entities.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)
[![Tests](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml/badge.svg)](https://github.com/georgii-web/php-typed-values/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/georgii-web/php-typed-values.svg?style=flat-square)](https://packagist.org/packages/georgii-web/php-typed-values)

---

### Install

- PHP 8.2+:

```bash
composer require georgii-web/php-typed-values:^2.0
```

- PHP 7.4:

```bash
composer require georgii-web/php-typed-values:^1.0
```

### Why

- Strong typing for scalars with runtime validation
- Immutable and self‑documenting values
- Safer constructors for your DTOs/VOs/Entities
- Great fit for static analysis (Psalm/PHPStan)

### Quick start

#### Use existing typed values

```php
use TypedValues\Integer\IntegerPositive;

$id = IntegerPositive::fromString('123');
```

Instead of spreading validation across an application:

```php
$id = (int) '123';
if ($id <= 0) {
    throw new InvalidArgumentException('Invalid ID');
}
```

#### Create an alias (domain name)

```php
use TypedValues\Integer\IntegerPositive;

readonly class Id extends IntegerPositive {}

Id::fromInt(123);
```

#### Compose value objects

```php
use TypedValues\Integer\IntegerPositive;
use TypedValues\String\StringNonEmpty;
use TypedValues\Float\FloatPositive;
use TypedValues\Undefined\Alias\Undefined; // represents an intentionally missing value

final readonly class Profile
{
    public function __construct(
        private IntegerPositive $id,
        private StringNonEmpty|Undefined $firstName,
        private FloatPositive|Undefined $height,
    ) {}

    public static function fromScalars(
        int $id,
        ?string $firstName,
        string|float|int|null $height = null,
    ): self {
        return new self(
            IntegerPositive::fromInt($id),                   // early fail (must be valid)
            StringNonEmpty::tryFromMixed($firstName),         // late fail (maybe undefined)
            $height !== null
                ? FloatPositive::fromString((string) $height) // early fail if provided
                : Undefined::create(),                        // late fail when accessed
        );
    }

    public function getFirstName(): StringNonEmpty|Undefined { return $this->firstName; }
    public function getHeight(): FloatPositive|Undefined { return $this->height; }
}
```

##### Early fail (invalid input prevents creation)

```php
Profile::fromScalars(id: 0, firstName: 'Alice', height: '172.5'); // throws exception
```

##### Late fail with `Undefined` (object exists, fail on access)

```php
$profile = Profile::fromScalars(id: 101, firstName: '', height: '172.5'); // created
$profile->getFirstName()->value(); // throws an exception on access the Undefined value
```

##### Optional fail (only fail if the optional value is provided and invalid)

```php
Profile::fromScalars(id: 101, firstName: 'Alice', height: -1); // invalid provided value -> early fail

$profile = Profile::fromScalars(id: 101, firstName: 'Alice', height: null); // value omitted -> created, fails only on access
$profile->getHeight()->value(); // throws an exception on access the Undefined value
```

### Key features

- Static analysis friendly (Psalm/PHPStan-ready types)
- Strict types with `declare(strict_types=1);`
- Validation on construction; no invalid state
- Immutable, readonly objects
- No external runtime dependencies
- Easy to extend with your own types and composites

### Performance note

- Objects vs scalars:
    - ~3× slower for large arrays of objects
    - ~2× higher memory usage
- Use value objects for domain boundaries, validation, and clarity
- Use raw scalars in hot loops or large data processing paths

### Documentation

- Development guide: `docs/DEVELOP.md`
- Usage examples in `src/Usage`

### License

MIT