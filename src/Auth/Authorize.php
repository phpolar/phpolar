<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Attribute;
use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;

/**
 * Use to indicate that a route should
 * only be accessed by an authorized user.
 *
 * Declare this attribute on the process method of
 * each Routable that requires authorization.
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Authorize
{
    /**
     * Return the target Routable when the request
     * has been authenticated.  Otherwise, return
     * the fallback Routable.
     */
    public function getResolvedRoutable(
        AbstractProtectedRoutable $target,
        AuthenticatorInterface $authenticator,
    ): RequestProcessorInterface | false {
        return $authenticator->isAuthenticated() === false ? false : $target->withUser((object) $authenticator->getUser());
    }
}
