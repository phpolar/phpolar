<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Declares the capabilities of the resource server.
 */
final readonly class Server implements ServerInterface
{
    /**
     * @param Target[] $interface
     */
    public function __construct(
        private array $interface,
    ) {}

    /**
     * Attempts to locate an object associated with a given route.
     *
     * The object defines an action that will be executed for
     * HTTP requests that match the associated route.
     */
    public function findTarget(ServerRequestInterface $request): Target | HttpResponseCode
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        $targetMatches = static fn(Target $target) => $target->matchesLocation($path);
        $methodMatches = static fn(Target $target) => $target->matchesMethod($method);
        $targetsMatching = array_filter($this->interface, $targetMatches(...));
        $resourceFound = array_any($this->interface, $targetMatches(...));
        $methodAllowed = array_any($targetsMatching, $methodMatches(...));

        if ($resourceFound === false) {
            return HttpResponseCode::NotFound;
        }

        if ($methodAllowed === false) {
            return HttpResponseCode::MethodNotAllowed;
        }

        return $this->getTarget($method, $targetsMatching);
    }

    /**
     * @param array<int,Target> $targetsMatching
     */
    private function getTarget(string $method, array $targetsMatching): Target
    {
        $key = array_find_key($targetsMatching, static fn(Target $target) => $target->matchesMethod($method));
        return $this->interface[$key];
    }
}
