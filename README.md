<p style="text-align:center">
    <img width="240" src="./phpolar.svg" />
</p>

# Polar

## A super-tiny, lightweight microframework for PHP projects

[![Coverage Status](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main)](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main) [![Latest Stable Version](http://poser.pugx.org/phpolar/phpolar/v)][def] [![Total Downloads](http://poser.pugx.org/phpolar/phpolar/downloads)][def] [![Latest Unstable Version](http://poser.pugx.org/phpolar/phpolar/v/unstable)][def] [![License](http://poser.pugx.org/phpolar/phpolar/license)][def] [![PHP Version Require](http://poser.pugx.org/phpolar/phpolar/require/php)][def]

### Objectives

1. Provide [attributes](#use-attributes-to-configure-models) so that objects can be declaratively configured for clean application development.
1. Support using [pure PHP templates](#pure-php-templates) with automatic XSS mitigation.

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
    <head>
        <style>
            body {
                font-family: <?= $view->font ?>;
                padding: 0;
                margin: 0;
            }
            form th {
                text-align: right;
            }
            form  td {
                text-align: left;
            }
            .container {
                background-color: <?= $view->backgroundColor ?>;
                padding: 20px 0 90px
            }
        </style>
    </head>
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

### Use Annotations to Configure Models

```php
use Efortmeyer\Polar\Api\Model;

class Person extends Model
{
    /**
     * @var string
     * @MaxLength(20)
     */
    public $firstName;

    /**
     * @var string
     * @MaxLength(20)
     */
    public $lastName;

    /**
     * @var string
     * @Column("Residential Address")
     * @Label("Residential Address")
     * @MaxLength(200)
     */
    public $address1;

    /**
     * @var string
     * @Column("Business Address")
     * @Label("Business Address")
     * @MaxLength(200)
     */
    public $address2;

    /**
     * @var DateTimeImmutable
     * @DateFormat(Y-m-d)
     */
    public $dateOfBirth;

    /**
     * @var DateTimeImmutable
     * @DateFormat("Y-m-d h:i:s a")
     */
    public $enteredOn;

    /**
     * @var DateTimeImmutable
     * @DateFormat("Y-m-d h:i:s a")
     */
    public $modifiedOn;
}
```

[API Documentation](https://ericfortmeyer.github.io/polar-docs)

[def]: https://packagist.org/packages/phpolar/phpolar
