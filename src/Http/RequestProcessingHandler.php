<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Phpolar\HttpRequestProcessor\RequestProcessorExecutorInterface;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles request routing for the application.
 */
final readonly class RequestProcessingHandler implements RequestHandlerInterface
{
    public function __construct(
        private ServerInterface $server,
        private RequestProcessorExecutorInterface $processorExecutor,
        private StreamFactoryInterface $streamFactory,
        private ResponseFactoryInterface $responseFactory,
        private RequestAuthorizerInterface $requestAuthorizer,
        private PropertyInjectorInterface $propertyInjector,
        private ModelResolverInterface $modelResolver,
        private ResponseCodeResolver $responseCodeResolver,
    ) {}

    /**
     * Attempts to locate and execute the target request processor.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * Find the target for the request.
         *
         * Respond that the resource could not be found
         * or the method is not acceptable
         * or continue
         */
        $target = $this->server->findTarget($request);
        if ($target instanceof HttpResponseCode) {
            return $this->responseFactory->createResponse(
                (int) $target->value,
                $target->getLabel()
            );
        }

        /**
         * Determine what representation will be used.
         * Respond that the request is not acceptable
         * or continue
         */
        $responseCode = $target->negotiate($request);
        if ($responseCode === HttpResponseCode::NotAcceptable) {
            return $this->responseFactory
                ->createResponse((int) $responseCode->value, $responseCode->getLabel());
        }

        $requestProcessor = $target->requestProcessor;

        $authorizeResult = $this->requestAuthorizer->authorize($requestProcessor, $request);
        if ($authorizeResult instanceof ResponseInterface) {
            /**
             * Respond with "not authorized" message if the request is not authorized
             */
            return $authorizeResult;
        }

        $authorized = $authorizeResult;

        /**
         * Get path variables
         */
        $pathVariables = new PathVariableBindings(
            $target->location,
            $request->getUri()->getPath()
        );

        /**
         * Get model from parsed body
         */
        $models = $this->modelResolver->resolve(
            $requestProcessor,
            "process",
        );

        /**
         * Merge path variables and models
         */
        $args = array_merge(
            $models,
            $pathVariables->toArray(),
        );

        /**
         * Handle property dependency injection
         */
        $this->propertyInjector->inject($authorized);

        $resource = $this->processorExecutor->execute(
            $authorized,
            $args,
        );


        $responseCode = $this->responseCodeResolver->resolve(
            resource: $resource,
            default: $responseCode,
        );

        $representation = $target->getRepresentation($resource);

        return $this->responseFactory
            ->createResponse(
                code: (int) $responseCode->value,
                reasonPhrase: $responseCode->getLabel()
            )
            ->withBody($this->streamFactory->createStream((string) $representation))
            ->withHeader("Content-Type", $representation->getMimeType());
    }
}
