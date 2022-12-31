<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\ServerRequest;

class ServerRequestTest extends BaseServerRequestTest
{
    protected function createSubject(): ServerRequestInterface
    {
        return new ServerRequest();
    }

    protected function createSubjectWithProtocolVersion(string $protocolVersion): ServerRequestInterface
    {
        return new ServerRequest($protocolVersion);
    }

    protected function createSubjectWithMethod(string $method): ServerRequestInterface
    {
        return new ServerRequest(null, $method);
    }

    protected function createSubjectWithUri($uri): ServerRequestInterface
    {
        return new ServerRequest(null, null, $uri);
    }

    protected function createSubjectWithHeaders(array $headers): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, $headers);
    }

    protected function createSubjectWithBody(StreamInterface $body): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, $body);
    }

    protected function createSubjectWithServerParams(array $serverParams): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, $serverParams);
    }

    protected function createSubjectWithQueryParams(array $queryParams): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, [], $queryParams);
    }

    protected function createSubjectWithCookieParams(array $cookieParams): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, [], [], $cookieParams);
    }

    protected function createSubjectWithUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, [], [], [], $uploadedFiles);
    }

    protected function createSubjectWithParsedBody($parsedBody): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, [], [], [], [], $parsedBody);
    }

    protected function createSubjectWithAttributes(array $attributes): ServerRequestInterface
    {
        return new ServerRequest(null, null, null, null, null, [], [], [], [], null, $attributes);
    }
}
