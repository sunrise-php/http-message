<?php

declare(strict_types=1);

namespace Request;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Request\JsonRequest;

use const JSON_FORCE_OBJECT;

class JsonRequestTest extends TestCase
{
    public function testConstructorWithJsonData(): void
    {
        $request = new JsonRequest('POST', '/', ['foo']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/', (string) $request->getUri());
        $this->assertSame('application/json; charset=utf-8', $request->getHeaderLine('Content-Type'));
        $this->assertStringStartsWith('php://temp', $request->getBody()->getMetadata('uri'));
        $this->assertTrue($request->getBody()->isReadable());
        $this->assertTrue($request->getBody()->isWritable());
        $this->assertSame('["foo"]', $request->getBody()->__toString());
    }

    public function testConstructorWithJsonFlags(): void
    {
        $request = new JsonRequest('POST', '/', [], JSON_FORCE_OBJECT);

        $this->assertSame('{}', $request->getBody()->__toString());
    }

    public function testConstructorWithInvalidJson(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unable to create the JSON request due to an invalid data: ' .
            'Maximum stack depth exceeded'
        );

        new JsonRequest('POST', '/', [], 0, 0);
    }

    public function testConstructorWithStream(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $request = new JsonRequest('POST', '/', $body);

        $this->assertSame($body, $request->getBody());
    }
}
