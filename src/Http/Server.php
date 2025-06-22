<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Psr\Http\Message\ServerRequestInterface;

final class Server implements ServerInterface
{
    /**
     * @param Target[] $interface
     */
    public function __construct(
        private readonly array $interface,
    ) {
    }

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
     * @param array<int, Target> $targetsMatching
     */
    private function getTarget(string $method, array $targetsMatching): Target
    {
        return $this->interface[array_find_key($targetsMatching, static fn(Target $target) => $target->matchesMethod($method))];
    }
}
