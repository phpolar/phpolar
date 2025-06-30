<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use Generator;
use PhpCommonEnums\HttpMethod\Enumeration\HttpMethodEnum as HttpMethod;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use PhpCommonEnums\MimeType\Enumeration\MimeTypeEnum as MimeType;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
use Phpolar\HttpMessageTestUtils\UriStub;
use Phpolar\HttpRequestProcessor\RequestProcessorExecutorInterface;
use Phpolar\HttpRequestProcessor\RequestProcessorInterface;
use Phpolar\ModelResolver\ModelResolverInterface;
use Phpolar\Phpolar\Http\Status\ClientError\BadRequest;
use Phpolar\Phpolar\Http\Status\ClientError\Forbidden;
use Phpolar\Phpolar\Http\Status\ClientError\NotFound;
use Phpolar\Phpolar\Http\Status\ClientError\Unauthorized;
use Phpolar\PropertyInjectorContract\PropertyInjectorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesClassesThatImplementInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(RequestProcessingHandler::class)]
#[CoversClass(PathVariableBindings::class)]
#[UsesClassesThatImplementInterface(ServerInterface::class)]
#[UsesClass(AuthorizationChecker::class)]
#[UsesClass(Representations::class)]
#[UsesClass(Target::class)]
#[UsesClass(RequestProcessorExecutor::class)]
final class RequestProcessingHandlerTest extends TestCase
{
    public static function requestMethods(): Generator
    {
        yield ["GET"];
        yield ["POST"];
    }

    #[TestDox("Shall respond with \"Not Found\" if the route is not registered for \$requestMethod requests")]
    #[DataProvider("requestMethods")]
    public function test1(string $requestMethod)
    {
        $request = (new RequestStub($requestMethod))->withUri(new UriStub(uniqid()));
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $serverStub
            ->method("findTarget")
            ->willReturn(HttpResponseCode::NotFound);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::NotFound->value);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: new RequestProcessorExecutor(),
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $response = $sut->handle($request);

        $this->assertSame(HttpResponseCode::NotFound->value, $response->getStatusCode());
    }

    #[TestDox("Shall respond with \"Not Acceptable\" the response code returned by Target::negotiate")]
    public function test1a()
    {
        $location = uniqid();
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::ApplicationJson]),
            requestProcessor: $this->createStub(RequestProcessorInterface::class),
        );
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::NotAcceptable->value);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: new RequestProcessorExecutor(),
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $response = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::NotAcceptable->value, $response->getStatusCode());
    }

    #[TestDox("Shall respond with the auth check result when auth check returns a response instead of a routable")]
    public function test1b()
    {
        $location = uniqid();
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $this->createStub(RequestProcessorInterface::class),
        );
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Unauthorized->value);
        $authCheckStub
            ->method("authorize")
            ->willReturn($responseStub);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: new RequestProcessorExecutor(),
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $response = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::Unauthorized->value, $response->getStatusCode());
    }

    #[TestDox("Shall respond with the auth check result when auth check returns a response instead of a routable")]
    public function test1c()
    {
        $content = "<h1>text</h1>";
        $location = uniqid();
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $requestProcessorStub,
        );
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Ok->value);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $requestProcessorStub
            ->method("process")
            ->willReturn($content);
        $streamStub
            ->method("getContents")
            ->willReturn($content);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: new RequestProcessorExecutor(),
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $response = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::Ok->value, $response->getStatusCode());
        $this->assertSame($content, $response->getBody()->getContents());
    }

    #[TestDox("Shall provide the path variables as arguments when executing the request processor")]
    #[TestWith(["/path/{name}/{id}", "/path/SOME_NAME/123", ["id" => "123", "name" => "SOME_NAME"]])]
    public function test1d(string $location, string $requestPath, array $expectedArgs)
    {
        $content = "";
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestProcessorExecutorMock = $this->createMock(RequestProcessorExecutorInterface::class);
        $requestProcessorExecutorMock
            ->expects($this->once())
            ->method("execute")
            ->with(
                $requestProcessorStub,
                $expectedArgs,
            )
            ->willReturn($content);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $requestProcessorStub,
        );
        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Ok->value);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $requestProcessorStub
            ->method("process")
            ->willReturn($content);
        $streamStub
            ->method("getContents")
            ->willReturn($content);
        $uriStub
            ->method("getPath")
            ->willReturn($requestPath);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $requestProcessorExecutorMock,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $sut->handle($requestStub);
    }

    #[TestDox("Shall provide the model as an argument when executing the request processor")]
    #[TestWith(["/path", "/path", ["id" => "123", "name" => "SOME_NAME"], ["id" => "123", "name" => "SOME_NAME"]])]
    public function test1e(string $location, string $requestPath, array $modelVars, array $expectedArgs)
    {
        $content = "";
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestProcessorExecutorMock = $this->createMock(RequestProcessorExecutorInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $requestProcessorStub,
        );

        $requestProcessorExecutorMock
            ->expects($this->once())
            ->method("execute")
            ->with(
                $requestProcessorStub,
                $expectedArgs,
            )
            ->willReturn($content);

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Ok->value);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $requestProcessorStub
            ->method("process")
            ->willReturn($content);
        $streamStub
            ->method("getContents")
            ->willReturn($content);
        $uriStub
            ->method("getPath")
            ->willReturn($requestPath);
        $modelResolverStub
            ->method("resolve")
            ->willReturn($modelVars);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $requestProcessorExecutorMock,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $sut->handle($requestStub);
    }

    #[TestDox("Shall provide the path variables as arguments when executing the request processor")]
    #[TestWith(["/path/{id}", "/path/123",  ["id" => "123"]])]
    public function test1f(string $location, string $requestPath, array $expectedArgs)
    {
        $content = "";
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestProcessorExecutorMock = $this->createMock(RequestProcessorExecutorInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $requestProcessorStub,
        );

        $requestProcessorExecutorMock
            ->expects($this->once())
            ->method("execute")
            ->with(
                $requestProcessorStub,
                $expectedArgs,
            )
            ->willReturn($content);

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Ok->value);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $requestProcessorStub
            ->method("process")
            ->willReturn($content);
        $streamStub
            ->method("getContents")
            ->willReturn($content);
        $uriStub
            ->method("getPath")
            ->willReturn($requestPath);
        $modelResolverStub
            ->method("resolve")
            ->willReturn([]);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $requestProcessorExecutorMock,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $sut->handle($requestStub);
    }

    #[TestDox("Shall provide the path variables as arguments that override model variables when executing the request processor")]
    #[TestWith(["/path/{id}", "/path/123", ["id" => "9999", "name" => "SOME_NAME"], ["id" => "123", "name" => "SOME_NAME"]])]
    public function test1g(string $location, string $requestPath, array $resolvedModelVars, array $expectedArgs)
    {
        $content = "";
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $responseStub = $this->createStub(ResponseInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);
        $streamStub = $this->createStub(StreamInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $requestProcessorExecutorMock = $this->createMock(RequestProcessorExecutorInterface::class);
        $target = new Target(
            location: $location,
            method: HttpMethod::Get,
            representations: new Representations([MimeType::TextHtml]),
            requestProcessor: $requestProcessorStub,
        );

        $requestProcessorExecutorMock
            ->expects($this->once())
            ->method("execute")
            ->with(
                $requestProcessorStub,
                $expectedArgs,
            )
            ->willReturn($content);

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::TextHtml->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn($responseStub);
        $responseStub
            ->method("withStatus")
            ->willReturn($responseStub);
        $responseStub
            ->method("withHeader")
            ->willReturn($responseStub);
        $responseStub
            ->method("getStatusCode")
            ->willReturn(HttpResponseCode::Ok->value);
        $responseStub
            ->method("getBody")
            ->willReturn($streamStub);
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $requestProcessorStub
            ->method("process")
            ->willReturn($content);
        $streamStub
            ->method("getContents")
            ->willReturn($content);
        $uriStub
            ->method("getPath")
            ->willReturn($requestPath);
        $modelResolverStub
            ->method("resolve")
            ->willReturn($resolvedModelVars);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $requestProcessorExecutorMock,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $sut->handle($requestStub);
    }

    #[TestDox("Shall produce a NOT FOUND status when the request processor returns an instance of NotFound")]
    public function test2a()
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $processorExecutorStub =
            $this->createStub(RequestProcessorExecutorInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);

        $processorExecutorStub
            ->method("execute")
            ->willReturn(new NotFound());


        $target = new Target(
            location: "/",
            method: HttpMethod::Get,
            representations: new Representations([MimeType::ApplicationJson]),
            requestProcessor: $requestProcessorStub,
        );

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::ApplicationJson->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn(new ResponseStub());
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $modelResolverStub
            ->method("resolve")
            ->willReturn([]);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $processorExecutorStub,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $result = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::NotFound->value, $result->getStatusCode());
        $this->assertSame(HttpResponseCode::NotFound->getLabel(), $result->getReasonPhrase());
    }

    #[TestDox("Shall produce a UNAUTHORIZED status when the request processor returns an instance of Unauthorized")]
    public function test2b()
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $processorExecutorStub =
            $this->createStub(RequestProcessorExecutorInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);

        $processorExecutorStub
            ->method("execute")
            ->willReturn(new Unauthorized());


        $target = new Target(
            location: "/",
            method: HttpMethod::Get,
            representations: new Representations([MimeType::ApplicationJson]),
            requestProcessor: $requestProcessorStub,
        );

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::ApplicationJson->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn(new ResponseStub());
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $modelResolverStub
            ->method("resolve")
            ->willReturn([]);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $processorExecutorStub,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $result = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::Unauthorized->value, $result->getStatusCode());
        $this->assertSame(HttpResponseCode::Unauthorized->getLabel(), $result->getReasonPhrase());
    }

    #[TestDox("Shall produce a FORBIDDEN status when the request processor returns an instance of Forbidden")]
    public function test2c()
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $processorExecutorStub =
            $this->createStub(RequestProcessorExecutorInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);

        $processorExecutorStub
            ->method("execute")
            ->willReturn(new Forbidden());


        $target = new Target(
            location: "/",
            method: HttpMethod::Get,
            representations: new Representations([MimeType::ApplicationJson]),
            requestProcessor: $requestProcessorStub,
        );

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::ApplicationJson->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn(new ResponseStub());
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $modelResolverStub
            ->method("resolve")
            ->willReturn([]);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $processorExecutorStub,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $result = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::Forbidden->value, $result->getStatusCode());
        $this->assertSame(HttpResponseCode::Forbidden->getLabel(), $result->getReasonPhrase());
    }

    #[TestDox("Shall produce a BAD REQUEST status when the request processor returns an instance of BadRequest")]
    public function test2d()
    {
        $requestStub = $this->createStub(ServerRequestInterface::class);
        $uriStub = $this->createStub(UriInterface::class);
        $serverStub = $this->createStub(ServerInterface::class);
        $processorExecutorStub =
            $this->createStub(RequestProcessorExecutorInterface::class);
        $responseBuilderStub = $this->createStub(ResponseBuilderInterface::class);
        $authCheckStub = $this->createStub(AuthorizationCheckerInterface::class);
        $propertyInjectorStub = $this->createStub(PropertyInjectorInterface::class);
        $modelResolverStub = $this->createStub(ModelResolverInterface::class);
        $requestProcessorStub = $this->createStub(RequestProcessorInterface::class);

        $processorExecutorStub
            ->method("execute")
            ->willReturn(new BadRequest());


        $target = new Target(
            location: "/",
            method: HttpMethod::Get,
            representations: new Representations([MimeType::ApplicationJson]),
            requestProcessor: $requestProcessorStub,
        );

        $requestStub
            ->method("getHeader")
            ->willReturn([MimeType::ApplicationJson->value]);
        $requestStub
            ->method("getUri")
            ->willReturn($uriStub);
        $serverStub
            ->method("findTarget")
            ->willReturn($target);
        $responseBuilderStub
            ->method("build")
            ->willReturn(new ResponseStub());
        $authCheckStub
            ->method("authorize")
            ->willReturn($requestProcessorStub);
        $uriStub
            ->method("getPath")
            ->willReturn("/");
        $modelResolverStub
            ->method("resolve")
            ->willReturn([]);

        $sut = new RequestProcessingHandler(
            server: $serverStub,
            processorExecutor: $processorExecutorStub,
            responseBuilder: $responseBuilderStub,
            authChecker: $authCheckStub,
            propertyInjector: $propertyInjectorStub,
            modelResolver: $modelResolverStub,
        );

        $result = $sut->handle($requestStub);

        $this->assertSame(HttpResponseCode::BadRequest->value, $result->getStatusCode());
        $this->assertSame(HttpResponseCode::BadRequest->getLabel(), $result->getReasonPhrase());
    }
}
