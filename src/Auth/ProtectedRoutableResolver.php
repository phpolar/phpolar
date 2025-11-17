<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use ReflectionMethod;
use ReflectionAttribute;

/**
 * Returns either the given Routable or false when the user is not
 * authenticated.
 *
 * Authorization is *opt-in*.  Therefore, the given Routable
 * will be returned if it is not configured with
 * an Authorize attribute.
 *
 * @codeCoverageIgnore
 * @deprecated Use RestrictedAccessRequestProcessorResolver instead.
 */
final class ProtectedRoutableResolver implements RequestProcessorResolverInterface
{
    private const ROUTABLE_METHOD_NAME = "process";

    public function __construct(private readonly AuthenticatorInterface $authenticator) {}

    public function resolve(RequestProcessorInterface $target): RequestProcessorInterface | false
    {
        if ($target instanceof AbstractProtectedRoutable === false) {
            return $target;
        }

        $authenticateAttrs = $this->getAuthenticateAttributes($target);
        $isNotConfigured = empty($authenticateAttrs);

        if ($isNotConfigured === true) {
            return $target;
        }

        return $this->resolveRoutable(
            authenticateAttrs: $authenticateAttrs,
            target: $target,
        );
    }

    /**
     * @return ReflectionAttribute<Authorize>[]
     */
    private function getAuthenticateAttributes(RequestProcessorInterface $routable): array
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
    ): RequestProcessorInterface | false {
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
