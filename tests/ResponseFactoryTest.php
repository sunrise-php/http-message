<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\ResponseFactory;

class ResponseFactoryTest extends TestCase
{
    public function testContracts(): void
    {
        $factory = new ResponseFactory();

        $this->assertInstanceOf(ResponseFactoryInterface::class, $factory);
    }

    public function testCreateResponse(): void
    {
        $response = (new ResponseFactory)->createResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
    }

    public function testCreateResponseWithStatusCode(): void
    {
        $response = (new ResponseFactory)->createResponse(202);

        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame('Accepted', $response->getReasonPhrase());
    }

    public function testCreateResponseWithStatusCodeAndEmptyReasonPhrase(): void
    {
        $response = (new ResponseFactory)->createResponse(202, '');

        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame('Accepted', $response->getReasonPhrase());
    }

    public function testCreateResponseWithStatusCodeAndCustomReasonPhrase(): void
    {
        $response = (new ResponseFactory)->createResponse(202, 'Custom Reason Phrase');

        $this->assertSame(202, $response->getStatusCode());
        $this->assertSame('Custom Reason Phrase', $response->getReasonPhrase());
    }

    public function testCreateResponseWithUnknownStatusCode(): void
    {
        $response = (new ResponseFactory)->createResponse(599);

        $this->assertSame(599, $response->getStatusCode());
        $this->assertSame('Unknown Status Code', $response->getReasonPhrase());
    }

    public function testCreateResponseWithUnknownStatusCodeAndEmptyReasonPhrase(): void
    {
        $response = (new ResponseFactory)->createResponse(599, '');

        $this->assertSame(599, $response->getStatusCode());
        $this->assertSame('Unknown Status Code', $response->getReasonPhrase());
    }

    public function testCreateResponseWithUnknownStatusCodeAndReasonPhrase(): void
    {
        $response = (new ResponseFactory)->createResponse(599, 'Custom Reason Phrase');

        $this->assertSame(599, $response->getStatusCode());
        $this->assertSame('Custom Reason Phrase', $response->getReasonPhrase());
    }

    public function testCreateResponseWithStatusCodeLessThan100(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        (new ResponseFactory)->createResponse(99);
    }

    public function testCreateResponseWithStatusCodeGreaterThan599(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        (new ResponseFactory)->createResponse(600);
    }

    public function testCreateResponseWithStatusCodeAndInvalidReasonPhrase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP reason phrase');

        (new ResponseFactory)->createResponse(200, "\0");
    }
}
