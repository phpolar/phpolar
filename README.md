<p align="center">
    <img width="240" src="./phpolar.svg" />
</p>

# PHPolar

## A super-tiny, lightweight microframework for PHP projects

[![Coverage Status](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main)](https://coveralls.io/repos/github/phpolar/phpolar/badge.svg?branch=main) [![Latest Stable Version](http://poser.pugx.org/phpolar/phpolar/v)][def] [![Total Downloads](http://poser.pugx.org/phpolar/phpolar/downloads)][def] [![Latest Unstable Version](http://poser.pugx.org/phpolar/phpolar/v/unstable)][def] [![License](http://poser.pugx.org/phpolar/phpolar/license)][def] [![PHP Version Require](http://poser.pugx.org/phpolar/phpolar/require/php)][def]

### Quick start

```bash
# create an example application

composer create-project phpolar/skeleton <target-directory>
```

### Objectives

1. Provide [attributes](#use-attributes-to-configure-models) so that objects can be declaratively configured for clean application development.
1. Support using [pure PHP templates](#pure-php-templates) with automatic XSS mitigation.

### Pure PHP Templates

#### Example 1

```php
<!DOCTYPE html>
<?php

namespace MyApp;

use Phpolar\Phpolar\FormControlTypes;

/**
 * @var PersonForm
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
            <form action="<?= $view->action ?>" method="post">
                <?php foreach ($view as $propName => $propVal): ?>
                    <label><?= $view->getLabel($propName) ?></label>
                    <?php switch ($view->getFormControlType($propName)): ?>
                        <?php case FormControlTypes::Input: ?>
                            <input type="text" value="<?= $propVal ?>" />
                        <?php case FormControlTypes::Select: ?>
                            <select>
                                <?php foreach ($propVal as $name => $item): ?>
                                    <option value="<?= $item ?>"><?= $name ?></option>
                                <?php endforeach ?>
                            </select>
                    <?php endswitch ?>
                <?php endforeach ?>
            </form>
        </div>
    </body>
</html>
```
<select>
    <option value=>
</select>

### Use Attributes to Configure Models

```php
use Efortmeyer\Polar\Api\Model;

class Person extends Model
{
    public string $title = "Person Form";

    public string $action = "somewhere";

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

[API Documentation](https://phpolar.github.io/phpolar/)

[def]: https://packagist.org/packages/phpolar/phpolar
