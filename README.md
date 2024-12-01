<p align="center">
    <img width="240" src="./phpolar.svg" />
</p>

# Polar

## A minimal PHP framework

[![Coverage Status](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main)](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main) [![Latest Stable Version](http://poser.pugx.org/phpolar/phpolar/v)][def] [![Total Downloads](http://poser.pugx.org/phpolar/phpolar/downloads)][def] [![Latest Unstable Version](http://poser.pugx.org/phpolar/phpolar/v/unstable)][def] [![License](http://poser.pugx.org/phpolar/phpolar/license)][def] [![PHP Version Require](http://poser.pugx.org/phpolar/phpolar/require/php)][def]

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
|     phpolar    |        9 kB       |   200 kB   |      x    |
|  phpolar/core  |        4 kB       |            |      x    |
|  phplar/model  |       19 kB       |   108 kB   |           |
|     **TOTAL**  |     **32 kB**     | **308 kB** |           |

* Note: Does not include comments.

[def]: https://packagist.org/packages/phpolar/phpolar
