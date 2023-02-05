<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\HttpCodes\ResponseCode;
use Phpolar\PhpTemplating\TemplateEngine;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles 401 errors.
 */
final class Error401Handler implements RequestHandlerInterface
{
    public const DEFAULT_ERROR_MSG = "<h1>An error occured</h1>";

    public const DEFAULT_FORBIDDEN_TPL_PATH = "templates/401.phtml";

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private TemplateEngine $templateEngine,
    ) {
    }

    /**
     * Return a 401 response.
     *
     * @suppress PhanUnusedPublicFinalMethodParameter
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(ResponseCode::FORBIDDEN)->withBody(
            $this->streamFactory->createStream(
                $this->getResponseContent()
            )
        );
    }

    private function getResponseContent(): string
    {
        $result = $this->templateEngine->apply(self::DEFAULT_FORBIDDEN_TPL_PATH);
        return is_string($result) === false ? self::DEFAULT_ERROR_MSG : $result;
    }
}
