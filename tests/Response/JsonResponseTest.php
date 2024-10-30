<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Response;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Response\JsonResponse;

use const JSON_FORCE_OBJECT;

class JsonResponseTest extends TestCase
{
    public function testConstructorWithJsonData(): void
    {
        $response = new JsonResponse(400, []);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/json; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertStringStartsWith('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertTrue($response->getBody()->isWritable());
        $this->assertSame('[]', $response->getBody()->__toString());
    }

    public function testConstructorWithJsonFlags(): void
    {
        $response = new JsonResponse(200, [], JSON_FORCE_OBJECT);

        $this->assertSame('{}', $response->getBody()->__toString());
    }

    public function testConstructorWithInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to create the JSON response due to an invalid data: ' .
            'Maximum stack depth exceeded'
        );

        new JsonResponse(200, [], 0, 0);
    }

    public function testConstructorWithStream(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $response = new JsonResponse(200, $body);

        $this->assertSame($body, $response->getBody());
    }
}
