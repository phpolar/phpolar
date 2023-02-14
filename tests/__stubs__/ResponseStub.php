<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ResponseStub implements ResponseInterface
{
    private StreamInterface $body;

    private string $version = "1.1";

    /**
     * @var array<string,array<string>>
     */
    private array $headers = [];

    public function __construct(
        private int $statusCode = 200,
        private string $reasonPhrase = "",
    ) {
    }

    private static function t()
    {
        throw new Exception("Not implemented");
    }

    public function getAttribute($name, $default = null)
    {
        self::t();
    }

    public function getAttributes()
    {
        self::t();
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getCookieParams()
    {
        self::t();
    }

    public function getHeader($name)
    {
        self::t();
    }

    public function getHeaderLine($name)
    {
        self::t();
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function getRequestTarget()
    {
        self::t();
    }

    public function getServerParams()
    {
        self::t();
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getUploadedFiles()
    {
        self::t();
    }

    public function getUri()
    {
        self::t();
    }

    public function hasHeader($name)
    {
        self::t();
    }

    public function withAddedHeader($name, $value)
    {
        self::t();
    }

    public function withAttribute($name, $value)
    {
        self::t();
    }

    public function withBody(StreamInterface $body)
    {
        $copy = clone $this;
        $copy->body = $body;
        return $copy;
    }

    public function withHeader($name, $value)
    {
        $copy = clone $this;
        if (is_array($value) === true) {
            $copy->headers[$name] = [...$copy->headers[$name], ...$value];
            return $copy;
        }
        $copy->headers[$name][] = $value;
        return $copy;
    }

    public function withoutAttribute($name)
    {
        self::t();
    }

    public function withoutHeader($name)
    {
        self::t();
    }

    public function withProtocolVersion($version)
    {
        $copy = clone $this;
        $copy->version = $version;
        return $copy;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $copy = clone $this;
        $copy->statusCode = $code;
        $copy->reasonPhrase = $reasonPhrase;
        return $copy;
    }
}
