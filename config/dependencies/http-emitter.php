<?php

/**
 * This file contains all services/dependencies required
 * by the framework.  Setting up the dependencies when
 * the framework is bootstrapped allows its users
 * not to have to worry about it.
 *
 * The framework is any PSR-11 container for
 * interoperability with other frameworks and to allow
 * users to use whatever implementation they want.
 */

declare(strict_types=1);

use Phpolar\Phpolar\DependencyInjection\DiTokens;

return [
    DiTokens::RESPONSE_EMITTER => new Laminas\HttpHandlerRunner\Emitter\SapiEmitter(),
];
