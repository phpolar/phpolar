<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Core\Formats;
use Phpolar\PurePhp\TemplateEngine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles 401 errors.
 */
final class ErrorHandler implements RequestHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

    private TemplateEngine $templateEngine;

    public function __construct(
        private int $responseCode,
        private string $reasonPhrase,
        ContainerInterface $container,
    ) {
        /**
         * @var ResponseFactoryInterface $responseFactory
         */
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        /**
         * @var StreamFactoryInterface $streamFactory
         */
        $streamFactory = $container->get(StreamFactoryInterface::class);
        /**
         * @var TemplateEngine $templateEng
         */
        $templateEng = $container->get(TemplateEngine::class);
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->templateEngine = $templateEng;
    }

    /**
     * Return a 401 response.
     *
     * @suppress PhanUnusedPublicFinalMethodParameter
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(
            $this->responseCode,
            $this->reasonPhrase,
        )->withBody(
            $this->streamFactory->createStream(
                $this->getResponseContent()
            )
        );
    }

    private function getResponseContent(): string
    {
        $result = $this->templateEngine->apply(sprintf(Formats::ErrorTemplates->value, $this->responseCode));
        return is_string($result) === false ? sprintf(Formats::ErrorText->value, $this->reasonPhrase) : $result;
    }
}
