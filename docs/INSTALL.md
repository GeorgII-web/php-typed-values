Install
======

Requirements
------------

- PHP 8.2 or newer
- Composer 2.x

Installation
------------

Add the package via Composer:

```
composer require georgii-web/php-typed-values
```

That’s it. Composer will autoload classes under the namespace:

```
GeorgiiWeb\PhpTypedValues
```

Quick verification
------------------

Create a quick test script (e.g., demo.php):

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use GeorgiiWeb\PhpTypedValues\Types\Integer\PositiveInt;

$age = new PositiveInt(21);
echo $age->value(); // 21
```

Run it:

```
php demo.php
```

If you see 21 printed, the library is installed correctly.
