<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Phpolar\HttpRequestProcessor\RequestProcessorExecutorInterface;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Http\Status\ClientError\BadRequest;
use Phpolar\Phpolar\Http\Status\ClientError\Forbidden;
use Phpolar\Phpolar\Http\Status\ClientError\NotFound;
use Phpolar\Phpolar\Http\Status\ClientError\Unauthorized;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles request routing for the application.
 */
final class RequestProcessingHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ServerInterface $server,
        private readonly RequestProcessorExecutorInterface $processorExecutor,
        private readonly ResponseBuilderInterface $responseBuilder,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly PropertyInjectorInterface $propertyInjector,
        private readonly ModelResolverInterface $modelResolver,
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
            return $this->responseBuilder
                ->build()
                ->withStatus((int) $target->value, $target->getLabel());
        }

        /**
         * Determine what representation will be used.
         * Respond that the request is not acceptable
         * or continue
         */
        $responseCode = $target->negotiate($request);
        if ($responseCode === HttpResponseCode::NotAcceptable) {
            return $this->responseBuilder
                ->build()
                ->withStatus((int) $responseCode->value, $responseCode->getLabel());
        }

        $requestProcessor = $target->requestProcessor;

        /**
         * Use "not authorized" response if the request is not authorized
         * or continue
         */
        $authCheckResult = $this->authChecker->authorize($requestProcessor, $request);
        if ($authCheckResult instanceof ResponseInterface) {
            return $authCheckResult;
        }

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
        $this->propertyInjector->inject($requestProcessor);

        $resource = $this->processorExecutor->execute(
            $requestProcessor,
            $args,
        );

        if ($resource instanceof NotFound) {
            $responseCode = HttpResponseCode::NotFound;
        }

        if ($resource instanceof Unauthorized) {
            $responseCode = HttpResponseCode::Unauthorized;
        }

        if ($resource instanceof Forbidden) {
            $responseCode = HttpResponseCode::Forbidden;
        }

        if ($resource instanceof BadRequest) {
            $responseCode = HttpResponseCode::BadRequest;
        }

        $representation = $target->getRepresentation($resource);

        return $this->responseBuilder->build(content: (string) $representation)
            ->withStatus(
                code: (int) $responseCode->value,
                reasonPhrase: $responseCode->getLabel()
            )
            ->withHeader("Content-Type", $representation->getMimeType());
    }
}
