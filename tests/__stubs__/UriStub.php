<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Exception;
use Psr\Http\Message\UriInterface;

final class UriStub implements UriInterface
{
    public function __construct(
        private string $path,
        private string $scheme = "http",
        private string $userInfo = "",
        private string $authority = "",
        private string $fragment = "",
        private string $host = "",
        private int $port = 0,
        private string $query = "",
    )
    {

    }
    private static function t(): never
    {
        throw new Exception("not implemented");
    }

    public function getAuthority()
    {
        return self::t();
    }

    public function getFragment()
    {
        return self::t();
    }

    public function getHost()
    {
        return self::t();
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPort()
    {
        return self::t();
    }

    public function getQuery()
    {
        return self::t();
    }

    public function getScheme()
    {
        return self::t();
    }

    public function getUserInfo()
    {
        return self::t();
    }

    public function withFragment($fragment)
    {
        return self::t();
    }

    public function withHost($host)
    {
        return self::t();
    }

    public function withPath($path)
    {
        $copy = clone $this;
        $copy->path = $path;
        return $copy;
    }

    public function withPort($port)
    {
        return self::t();
    }

    public function withQuery($query)
    {
        return self::t();
    }

    public function withScheme($scheme)
    {
        return self::t();
    }

    public function withUserInfo($user, $password = null)
    {
        return self::t();
    }

    public function __toString()
    {
        return sprintf(
            "%s://%s",
            $this->scheme,
            $this->userInfo
                . $this->authority
                . $this->path
                . (("" === $this->fragment ?: "#") . $this->fragment)
                . (("" === $this->query ?: "?") . $this->query),
        );
    }
}
