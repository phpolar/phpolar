<?php

declare(strict_types=1);

$files = glob(__DIR__ . DIRECTORY_SEPARATOR . "src/*.php");

if (is_array($files) === true) {
    array_walk(
        $files,
        function (string $filename) {
            require_once $filename;
        }
    );
}
