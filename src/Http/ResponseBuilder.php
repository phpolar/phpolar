<?php

namespace Phpolar\Phpolar\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @codeCoverageIgnore
 * 
 * @suppress PhanDeprecatedInterface
 * @deprecated
 */
final class ResponseBuilder implements ResponseBuilderInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function build(string $content = ""): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse()
            ->withBody(
                $this->streamFactory->createStream($content)
            );
    }
}
