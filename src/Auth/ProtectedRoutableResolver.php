<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use Phpolar\Authenticator\AuthenticatorInterface;
use Phpolar\Routable\RoutableInterface;
use Phpolar\Routable\RoutableResolverInterface;
use ReflectionMethod;
use ReflectionAttribute;

/**
 * Returns either the given routable or the given fallback handler
 * based on the boolean result of a given algorithm.
 *
 * Authentication is *opt-in*.  Therefore, the given routable
 * will be returned if it is not configured with
 * an `Authorize` attribute.
 */
final class ProtectedRoutableResolver implements RoutableResolverInterface
{
    private const ROUTABLE_METHOD_NAME = "process";

    public function __construct(private AuthenticatorInterface $authenticator)
    {
    }

    public function resolve(RoutableInterface $target): RoutableInterface | false
    {
        $isNotProtected = $target instanceof AbstractProtectedRoutable === false;

        if ($isNotProtected === true) {
            return $target;
        }

        $authenticateAttrs = $this->getAuthenticateAttributes($target);
        $isNotConfigured = empty($authenticateAttrs);

        if ($isNotConfigured === true) {
            return $target;
        }

        return $this->resolveRoutable(authenticateAttrs: $authenticateAttrs, target: $target);
    }

    /**
     * @return ReflectionAttribute<Authorize>[]
     */
    private function getAuthenticateAttributes(AbstractProtectedRoutable $routable): array
    {
        $reflectionMethod = new ReflectionMethod($routable, self::ROUTABLE_METHOD_NAME);
        return $reflectionMethod->getAttributes(Authorize::class);
    }

    /**
     * @param ReflectionAttribute<Authorize>[] $authenticateAttrs
     */
    private function resolveRoutable(
        array $authenticateAttrs,
        AbstractProtectedRoutable $target,
    ): RoutableInterface | false {
        /**
         * @var Authorize
         */
        $authenticateAttr = $authenticateAttrs[0]->newInstance();
        return $authenticateAttr->getResolvedRoutable(
            target: $target,
            authenticator: $this->authenticator,
        );
    }
}
