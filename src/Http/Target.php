<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\Routable\RoutableInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The target of an HTTP request.
 *
 * The target is sometimes referred to as the "resource".
 * This "resource" may have one or more representations.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-2
 */
final class Target
{
    /**
     * @var MimeType[]
     */
    private readonly array $accepted;

    public function __construct(
        public readonly string $location,
        private readonly HttpMethod $method,
        private readonly Representations $representations,
        public readonly RoutableInterface $requestProcessor,
    ) {
        $this->accepted = [
            MimeType::TextHtml,
            MimeType::ApplicationJson,
        ];
    }

    public function negotiate(
        ServerRequestInterface $request,
    ): HttpResponseCode {
        if ($this->representations->contains($request->getHeader("Accept")) === false) {
            return HttpResponseCode::NotAcceptable;
        }

        if ($this->representations->contains($this->getSupportedRepresentations()) === false) {
            return HttpResponseCode::NotAcceptable;
        }

        return $request->getMethod() === HttpMethod::Post->value ? HttpResponseCode::Created : HttpResponseCode::Ok;
    }

    public function getRepresentation(
        mixed $resource,
    ): RepresentationInterface {
        if ($this->representations->contains([MimeType::TextHtml->value]) === true) {
            return new HtmlRepresentation($resource);
        }

        if ($this->representations->contains([MimeType::ApplicationJson->value]) === true) {
            return new JsonRepresentation($resource);
        }
        return new HtmlRepresentation($resource);
    }

    public function matchesLocation(
        string $testLocation,
    ): bool {
        return $this->location === $testLocation
            || $this->matchesParameterizedPath($testLocation);
    }

    public function matchesMethod(
        string $method,
    ): bool {
        return $this->method->getLower() === strtolower($method);
    }

    private function matchesParameterizedPath(string $path): bool
    {
        return $this->containsPathVariables($this->location)
            && $this->partsMatch($path);
    }

    private function partsMatch(string $path): bool
    {
        $pathParts = explode("/", ltrim($path, "/"));
        $routeParts = explode("/", ltrim($this->location, "/"));
        $routePartsCnt = count($routeParts);
        if ($routePartsCnt !== count($pathParts)) {
            return false;
        }
        return count(
            array_filter(
                array_combine($routeParts, $pathParts),
                fn(string $pathPart, string $routePart) => $routePart === $pathPart || $this->containsPathVariables($routePart),
                ARRAY_FILTER_USE_BOTH,
            )
        ) === count($routeParts);
    }

    private function containsPathVariables(string $path): bool
    {
        return preg_match(PathVariableBindings::PATH_VARIABLE_PATTERN, $path) === 1;  // @codeCoverageIgnore
    }

    /**
     * @return string[]
     */
    private function getSupportedRepresentations(): array
    {
        return array_map(
            static fn(MimeType $mimeType) => $mimeType->value,
            $this->accepted,
        );
    }
}
