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
