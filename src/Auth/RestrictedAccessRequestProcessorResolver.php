<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Auth;

use PhpContrib\Authenticator\AuthenticatorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorResolverInterface;
use ReflectionMethod;
use ReflectionAttribute;

/**
 * Returns either the given RequestProcessor or false when the user is not
 * authenticated.
 *
 * Authorization is *opt-in*.  Therefore, the given RequestProcessor
 * will be returned if it is not configured with
 * an Authorize attribute.
 */
final readonly class RestrictedAccessRequestProcessorResolver implements RequestProcessorResolverInterface
{
    private const ROUTABLE_METHOD_NAME = "process";

    public function __construct(private readonly AuthenticatorInterface $authenticator) {}

    public function resolve(RequestProcessorInterface $target): RequestProcessorInterface | false
    {
        if ($target instanceof AbstractRestrictedAccessRequestProcessor === false) {
            return $target;
        }

        $authenticateAttrs = $this->getAuthenticateAttributes($target);
        $isNotConfigured = empty($authenticateAttrs);

        if ($isNotConfigured === true) {
            return $target;
        }

        return $this->resolveRoutable(
            authenticateAttr: $authenticateAttrs[0],
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
     * @param ReflectionAttribute<Authorize> $authenticateAttr
     */
    private function resolveRoutable(
        ReflectionAttribute $authenticateAttr,
        AbstractRestrictedAccessRequestProcessor $target,
    ): RequestProcessorInterface | false {
        /**
         * @var Authorize
         */
        $authorizeInstance = $authenticateAttr->newInstance();
        return $authorizeInstance->getResolvedRoutable(
            target: $target,
            authenticator: $this->authenticator,
        );
    }
}
