# Decimal-4-Math

Arithmetic operations on big and precise currency amounts.

## Why?

Because floats and doubles are too evil to handle money and I needed this myself.

### Prerequesites

```
PHP >= 5.4.0
ext-gmp
```

### Installing

Update packages and install gmp for your PHP:
```
$ apt-get update
$ apt-get upgrade
$ apt-get install php-gmp
```

Install Decimal-4-Math using [Composer package manager](https://packagist.org/):
```
{
    "require": {
        "spruur/decimal-4-math": "1.0.1"
    }
}
```

Install composer dependencies:
```
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

Add composer autoloader to your code:
```
<?php
require 'vendor/autoload.php';
```

## Usage
```
use Dec4Math\Decimal4;
    
$num1 = new Decimal4("0.5");
$num2 = new Decimal4("10.5601");
    
$num2->fractionValue();     // 5601
$num2->integerValue();      // 10
$num2->millsValue();        // 105601
    
echo Decimal4::plus($num1, $num2);      // 11.0601
echo Decimal4::minus($num2, $num1);     // 10.0601
echo Decimal4::multiply($num1, 2);      // 1.0000
echo Decimal4::mul($num1, $num2);       // 5.2800
```