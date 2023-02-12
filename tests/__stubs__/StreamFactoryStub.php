<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Exception;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactoryStub implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return new MemoryStreamStub($content);
    }
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        throw new Exception("not implemented");
    }
    public function createStreamFromResource($resource): StreamInterface
    {
        throw new Exception("not implemented");
    }
}
