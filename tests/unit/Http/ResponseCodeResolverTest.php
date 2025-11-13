<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as HttpResponseCode;
use Phpolar\Phpolar\Http\Status\ClientError\BadRequest;
use Phpolar\Phpolar\Http\Status\ClientError\Forbidden;
use Phpolar\Phpolar\Http\Status\ClientError\NotFound;
use Phpolar\Phpolar\Http\Status\ClientError\Unauthorized;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ResponseCodeResolver::class)]
final class ResponseCodeResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testResolveReturnsNotFoundWhenResourceIsNotFound(): void
    {
        $originalCode = HttpResponseCode::Ok;
        $resource = new NotFound();

        $resolvedCode = new ResponseCodeResolver()->resolve($originalCode, $resource);

        $this->assertSame(HttpResponseCode::NotFound, $resolvedCode);
    }

    public function testResolveReturnsBadRequestWhenResourceIsBadRequest(): void
    {
        $originalCode = HttpResponseCode::Ok;
        $resource = new BadRequest();

        $resolvedCode = new ResponseCodeResolver()->resolve($originalCode, $resource);

        $this->assertSame(HttpResponseCode::BadRequest, $resolvedCode);
    }

    public function testResolveReturnsUnauthorizedWhenResourceIsUnauthorized(): void
    {
        $originalCode = HttpResponseCode::Ok;
        $resource = new Unauthorized();

        $resolvedCode = new ResponseCodeResolver()->resolve($originalCode, $resource);

        $this->assertSame(HttpResponseCode::Unauthorized, $resolvedCode);
    }
    public function testResolveReturnsForbiddenWhenResourceIsForbidden(): void
    {
        $originalCode = HttpResponseCode::Ok;
        $resource = new Forbidden();

        $resolvedCode = new ResponseCodeResolver()->resolve($originalCode, $resource);

        $this->assertSame(HttpResponseCode::Forbidden, $resolvedCode);
    }

    public function testResolveReturnsOriginalCodeWhenResourceIsNoneOfTheAbove(): void
    {
        $originalCode = HttpResponseCode::Ok;
        $resource = new \stdClass();

        $resolvedCode = new ResponseCodeResolver()->resolve($originalCode, $resource);

        $this->assertSame($originalCode, $resolvedCode);
    }
}
