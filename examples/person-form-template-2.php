<?php

namespace MyApp;

use Phpolar\Phpolar\Model\FormControlTypes;
(function (Person $view) {
?>
<!DOCTYPE html>
<html>
    <body style="text-align:center">
        <h1><?= $view->title ?></h1>
        <div class="container">
            <form action="<?= $view->action ?>" method="post">
                <?php foreach ($view as $propName => $propVal): ?>
                    <p>
                        <label><?= $view->getLabel($propName) ?></label>
                        <?php switch ($view->getFormControlType($propName)): ?>
                            <?php case FormControlTypes::Input: ?>
                                <input
                                    type="<?= $view->getFormInputType($propName) ?>"
                                    name="<?= $propName ?>"
                                    value="<?= $propVal ?>" />
                            <?php case FormControlTypes::Select: ?>
                                <select name="<?= $propVal[0] ?>">
                                    <?php foreach ($propVal as $name => $item): ?>
                                        <option value="<?= $item ?>"><?= $name ?></option>
                                    <?php endforeach ?>
                                </select>
                        <?php endswitch ?>
                        <span class="error-message"><?= $view->getFieldErrorMessage($propName) ?></span>
                    </p>
                <?php endforeach ?>
            </form>
        </div>
    </body>
</html>
<?php
})($this);
