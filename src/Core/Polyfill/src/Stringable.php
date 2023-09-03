<?php

declare(strict_types=1);

if (
    \PHP_VERSION_ID < 80000 &&
    in_array("Stringable", get_declared_interfaces()) === false
) {
    interface Stringable
    {
        /**
         * @return string
         */
        public function __toString();
    }
}
