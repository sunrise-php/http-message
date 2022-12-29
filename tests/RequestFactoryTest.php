<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\RequestFactory;
use Sunrise\Http\Message\Uri;

class RequestFactoryTest extends TestCase
{
    public function testContracts(): void
    {
        $factory = new RequestFactory();

        $this->assertInstanceOf(RequestFactoryInterface::class, $factory);
    }

    public function testCreateRequest(): void
    {
        $uri = new Uri();

        $subject = (new RequestFactory)->createRequest('POST', $uri);

        $this->assertSame('POST', $subject->getMethod());
        $this->assertSame($uri, $subject->getUri());
    }

    public function testCreateRequestWithLowerCaseMethod(): void
    {
        $this->assertSame('post', (new RequestFactory)
            ->createRequest('post', new Uri())
            ->getMethod());
    }

    public function testCreateRequestWithNonStandardMethod(): void
    {
        $this->assertSame('CUSTOM', (new RequestFactory)
            ->createRequest('CUSTOM', new Uri())
            ->getMethod());
    }

    public function testCreateRequestWithEmptyMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method cannot be an empty');

        (new RequestFactory)->createRequest('', new Uri());
    }

    public function testCreateRequestWithInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        (new RequestFactory)->createRequest("GET\0", '/');
    }

    public function testCreateRequestWithStringUri(): void
    {
        $this->assertSame('/foo', (new RequestFactory)
            ->createRequest('GET', '/foo')
            ->getUri()
            ->__toString());
    }

    public function testCreateRequestWithInvalidUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to parse URI');

        (new RequestFactory)->createRequest('GET', ':');
    }
}
