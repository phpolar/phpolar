<p align="center">
    <img width="240" src="./phpolar.svg" />
</p>

# Polar

## A minimal PHP framework

[![Coverage Status](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main)](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main) [![Latest Stable Version](http://poser.pugx.org/phpolar/phpolar/v)][def] [![Total Downloads](http://poser.pugx.org/phpolar/phpolar/downloads)][def] [![Latest Unstable Version](http://poser.pugx.org/phpolar/phpolar/v/unstable)][def] [![License](http://poser.pugx.org/phpolar/phpolar/license)][def] [![PHP Version Require](http://poser.pugx.org/phpolar/phpolar/require/php)][def]

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
/**
 * @var Page $view
 */
$view = $this;
?>
<html>
    // ...
    <body style="text-align:center">
        <h1><?= $view->title ?></h1>
        <div class="container">
        </div>
    </body>
</html>
```

### Use Attributes to Configure Models

```php
use Efortmeyer\Polar\Api\Model;

class Person extends Model
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

    #[DateFormat("Y-m-d")]
    public DateTimeImmutable $dateOfBirth;

    #[DateFormat("Y-m-d h:i:s a")]
    public DateTimeImmutable $enteredOn;

    #[DateFormat("Y-m-d h:i:s a")]
    public DateTimeImmutable $modifiedOn;
}
```

### Thresholds
|Source Code Size|Memory Usage|
|----------------|------------|
|25.5 kB|750 kB|

[Quick Start](https://docs.phpolar.org/quick-start/)
[Documentation](https://docs.phpolar.org)
[API](https://api.phpolar.org)
[Website](https://phpolar.org)

[def]: https://packagist.org/packages/phpolar/phpolar
