<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Request;

class RequestTest extends BaseRequestTest
{
    protected function createSubject(): RequestInterface
    {
        return new Request();
    }

    protected function createSubjectWithMethod(string $method): RequestInterface
    {
        return new Request($method);
    }

    protected function createSubjectWithUri($uri): RequestInterface
    {
        return new Request(null, $uri);
    }

    protected function createSubjectWithHeaders(array $headers): RequestInterface
    {
        return new Request(null, null, $headers);
    }

    protected function createSubjectWithBody(StreamInterface $body): RequestInterface
    {
        return new Request(null, null, null, $body);
    }
}
