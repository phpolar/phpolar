<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Phpolar\Phpolar\Core\Formats;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles HTTP errors.
 */
final class ErrorHandler implements RequestHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

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
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Return an HTTP error response.
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
                $this->process()
            )
        );
    }

    private function process(): string
    {
        $errorTplFilename = sprintf(Formats::ErrorTemplates->value, $this->responseCode);
        $defaultErrorText = sprintf(Formats::ErrorText->value, $this->reasonPhrase);
        if (file_exists($errorTplFilename) === false) {
            return $defaultErrorText;
        }
        return (string) file_get_contents($errorTplFilename);
    }
}
