<p align="center">
    <img width="240" src="./phpolar.svg" alt="PHPolar logo" />
</p>

# PHPolar

## A PHP framework for minimalists

[![Coverage Status](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main)](https://coveralls.io/github/phpolar/phpolar?branch=main) [![Latest Stable Version](https://poser.pugx.org/phpolar/phpolar/v)][def] [![Total Downloads](https://poser.pugx.org/phpolar/phpolar/downloads)][def] [![License](https://poser.pugx.org/phpolar/phpolar/license)][def] [![PHP Version Require](https://poser.pugx.org/phpolar/phpolar/require/php)][def] [![Weekly Check](https://github.com/phpolar/phpolar/actions/workflows/weekly.yml/badge.svg)](https://github.com/phpolar/phpolar/actions/workflows/weekly.yml)

[Quick Start](https://docs.phpolar.org/quick-start/) <br/>
[Documentation](https://docs.phpolar.org) <br/>
[API](https://api.phpolar.org) <br/>
[Website](https://phpolar.org) <br/>

### Quick start

```bash
# create an example application

composer create-project phpolar/skeleton <target-directory>
```

### Objectives

1. Provide [attributes](#use-attributes-to-configure-models) so that objects can be declaratively configured for clean application development.
1. Support using [pure PHP templates](#pure-php-templates) with automatic XSS mitigation.
1. Keep project small. See [thresholds](#thresholds)

**Note** For more details see the [acceptance tests results](./acceptance-test-results.md)

### Pure PHP Templates

#### Example 1

```php
<!DOCTYPE html>
<?php
(function (Page $view) {
?>
<html>
    // ...
    <body style="text-align:center">
        <h1><?= $view->title ?></h1>
        <div class="container">
        </div>
    </body>
</html>
<?php
})($this);
```

### Use Attributes to Configure Models

```php
use Phpolar\Phpolar\AbstractModel;

class Person extends AbstractModel
{
    #[MaxLength(20)]
    public string $firstName;

    #[MaxLength(20)]
    public string $lastName;

    #[Column("Residential Address")]
    #[Label("Residential Address")]
    #[MaxLength(200)]
    public string $address1;

    #[Column("Business Address")]
    #[Label("Business Address")]
    #[MaxLength(200)]
    public string $address2;
}
```

### Thresholds

|      Module    |Source Code Size * |Memory Usage|  Required |
|----------------|-------------------|------------|-----------|
|     phpolar    |       13 kB       |   250 kB   |      x    |
|  phplar/model  |       19 kB       |   108 kB   |           |
|     **TOTAL**  |     **38 kB**     | **358 kB** |           |

* Note: Does not include comments.

[def]: https://packagist.org/packages/phpolar/phpolar
