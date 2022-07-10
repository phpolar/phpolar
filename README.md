# Polar

## A super-tiny, lightweight microframework for PHP projects

[![Coverage Status](https://coveralls.io/repos/github/ericfortmeyer/polar/badge.svg?branch=main)](https://coveralls.io/repos/github/ericfortmeyer/polar/badge.svg?branch=main)[![Latest Stable Version](http://poser.pugx.org/efortmeyer/polar/v)](https://packagist.org/packages/efortmeyer/polar) [![Total Downloads](http://poser.pugx.org/efortmeyer/polar/downloads)](https://packagist.org/packages/efortmeyer/polar) [![Latest Unstable Version](http://poser.pugx.org/efortmeyer/polar/v/unstable)](https://packagist.org/packages/efortmeyer/polar) [![License](http://poser.pugx.org/efortmeyer/polar/license)](https://packagist.org/packages/efortmeyer/polar) [![PHP Version Require](http://poser.pugx.org/efortmeyer/polar/require/php)](https://packagist.org/packages/efortmeyer/polar)

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


[API Documentation](https://ericfortmeyer.dev/projects/polar/api)
