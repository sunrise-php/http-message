<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sunrise\Http\Message\ResponseFactory;

/**
 * ResponseFactoryTest
 */
class ResponseFactoryTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $factory = new ResponseFactory();

        $this->assertInstanceOf(ResponseFactoryInterface::class, $factory);
    }

    /**
     * @return void
     */
    public function testCreateResponse() : void
    {
        $statusCode = 204;
        $reasonPhrase = 'No Content';

        $response = (new ResponseFactory)
            ->createResponse($statusCode, $reasonPhrase);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($reasonPhrase, $response->getReasonPhrase());

        // default body of the response...
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertTrue($response->getBody()->isSeekable());
        $this->assertTrue($response->getBody()->isWritable());
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertSame('php://temp', $response->getBody()->getMetadata('uri'));
    }

    /**
     * @return void
     */
    public function testCreateHtmlResponse() : void
    {
        $content = '<pre>foo bar</pre>';

        $response = (new ResponseFactory)
            ->createHtmlResponse(400, $content);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertSame($content, (string) $response->getBody());
    }

    /**
     * @return void
     */
    public function testCreateJsonResponse() : void
    {
        $payload = ['foo' => '<bar>'];
        $options = \JSON_HEX_TAG;

        $response = (new ResponseFactory)
            ->createJsonResponse(400, $payload, $options);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('application/json; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertSame(\json_encode($payload, $options), (string) $response->getBody());
    }

    /**
     * @return void
     */
    public function testCreateResponseWithInvalidJson() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum stack depth exceeded');

        $response = (new ResponseFactory)
            ->createJsonResponse(200, [[]], 0, 1);
    }
}
