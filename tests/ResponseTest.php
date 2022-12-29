<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Response;

class ResponseTest extends BaseResponseTest
{
    protected function createSubject(): ResponseInterface
    {
        return new Response();
    }

    protected function createSubjectWithStatus(int $statusCode, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($statusCode, $reasonPhrase);
    }

    protected function createSubjectWithHeaders(array $headers): ResponseInterface
    {
        return new Response(null, null, $headers);
    }

    protected function createSubjectWithBody(StreamInterface $body): ResponseInterface
    {
        return new Response(null, null, null, $body);
    }
}
