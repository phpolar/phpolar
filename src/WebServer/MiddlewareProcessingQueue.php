<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\WebServer;

use Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory;
use Phpolar\CsrfProtection\Http\CsrfPreRoutingMiddleware;
use Phpolar\Extensions\HttpResponse\ResponseExtension;
use Phpolar\Phpolar\WebServer\Http\ErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles middleware processing for HTTP requests
 */
final class MiddlewareProcessingQueue
{
    private bool $useCsrfProtection = false;

    /**
     * @var MiddlewareInterface[] Contains configured pre-routing middleware.
     */
    private array $preRoutingMiddleware = [];

    private CsrfPostRoutingMiddlewareFactory $csrfPostRouting;

    private RequestHandlerInterface $errorHandler;

    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * Adds support for CSRF mitigation.
     *
     * @throws WebServerConfigurationException
     */
    public function addCsrfMiddleware(
        CsrfPreRoutingMiddleware $csrfPreRouting,
        CsrfPostRoutingMiddlewareFactory $csrfPostRouting,
        RequestHandlerInterface $errorHandler,
    ): void {
        $this->csrfPostRouting = $csrfPostRouting;
        $this->errorHandler = $errorHandler;
        $this->preRoutingMiddleware[] = $csrfPreRouting;
        $this->useCsrfProtection = true;
    }

    /**
     * Processes all middleware configured to be processed
     * before request route handling.
     */
    public function dequeuePreRoutingMiddleware(
        ServerRequestInterface $request
    ): AbortProcessingRequest|ContinueProcessingRequest {
        $preRoutingResponses = array_map(
            fn (MiddlewareInterface $middleware): ResponseInterface =>
                $middleware->process($request, $this->errorHandler),
            $this->preRoutingMiddleware,
        );
        foreach ($preRoutingResponses as $preRoutingResponse) {
            if ($preRoutingResponse->getStatusCode() >= 400) {
                ResponseExtension::extend($preRoutingResponse)->send();
                return new AbortProcessingRequest();
            }
        }
        return new ContinueProcessingRequest();
    }

    /**
     * Process all middleware configured to be processed
     * after request route handling.
     */
    public function dequeuePostRoutingMiddleware(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        if ($response->getStatusCode() >= 400) {
            $errorHandler = new ErrorHandler(
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $this->container,
            );
            return $errorHandler->handle($request);
        }
        if ($this->useCsrfProtection === true) {
            return $this->csrfPostRouting->getMiddleware($response)
                ->process($request, $this->errorHandler);
        }
        return $response;
    }
}
