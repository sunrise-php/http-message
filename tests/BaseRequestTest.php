<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\RequestInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;
use Sunrise\Http\Message\Uri;

abstract class BaseRequestTest extends BaseMessageTest
{
    abstract protected function createSubject(): RequestInterface;

    protected function createSubjectWithMethod(string $method): ?RequestInterface
    {
        return null;
    }

    protected function createSubjectWithUri($uri): ?RequestInterface
    {
        return null;
    }

    public function testContracts(): void
    {
        $subject = $this->createSubject();

        $this->assertInstanceOf(RequestMethodInterface::class, $subject);
    }

    public function testDefaultMethod(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('GET', $subject->getMethod());
    }

    public function testDefaultUri(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('/', $subject->getUri()->__toString());
    }

    public function testDefaultRequestTarget(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testSetMethod(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withMethod('POST');

        $this->assertNotSame($clone, $subject);
        $this->assertSame('POST', $clone->getMethod());
        $this->assertSame('GET', $subject->getMethod());
    }

    public function testSetLowerCaseMethod(): void
    {
        $subject = $this->createSubject()->withMethod('post');

        $this->assertSame('post', $subject->getMethod());
    }

    public function testSetNonStandardMethod(): void
    {
        $subject = $this->createSubject()->withMethod('CUSTOM');

        $this->assertSame('CUSTOM', $subject->getMethod());
    }

    public function testSetMethodAsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method cannot be an empty');

        $this->createSubject()->withMethod('');
    }

    public function testSetMethodAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method must be a string');

        $this->createSubject()->withMethod(null);
    }

    public function testSetInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        $this->createSubject()->withMethod("GET\0");
    }

    public function testSetUri(): void
    {
        $uri = new Uri();
        $subject = $this->createSubject();
        $clone = $subject->withUri($uri);

        $this->assertNotSame($clone, $subject);
        $this->assertSame($uri, $clone->getUri());
        $this->assertNotSame($uri, $subject->getUri());
    }

    public function testSetUriAndHostHeader(): void
    {
        $uri = new Uri('//localhost');
        $subject = $this->createSubject();
        $clone = $subject->withUri($uri);

        $this->assertSame('localhost', $clone->getHeaderLine('Host'));
        $this->assertFalse($subject->hasHeader('Host'));
    }

    public function testSetUriWithPreservationHostHeader(): void
    {
        $uri = new Uri('//www2.localhost');
        $subject = $this->createSubject()->withHeader('Host', 'www1.localhost');
        $clone = $subject->withUri($uri, true);

        $this->assertNotSame($clone, $subject);
        $this->assertSame('www1.localhost', $clone->getHeaderLine('Host'));
        $this->assertSame('www1.localhost', $subject->getHeaderLine('Host'));
    }

    public function testSetUriWithoutPreservationHostHeader(): void
    {
        $uri = new Uri('//www2.localhost');
        $subject = $this->createSubject()->withHeader('Host', 'www1.localhost');
        $clone = $subject->withUri($uri, false);

        $this->assertNotSame($clone, $subject);
        $this->assertSame('www2.localhost', $clone->getHeaderLine('Host'));
        $this->assertSame('www1.localhost', $subject->getHeaderLine('Host'));
    }

    public function testSetUriWithPortWithoutPreservationHostHeader(): void
    {
        $uri = new Uri('//www2.localhost:8000');
        $subject = $this->createSubject()->withHeader('Host', 'www1.localhost');
        $clone = $subject->withUri($uri, false);

        $this->assertNotSame($clone, $subject);
        $this->assertSame('www2.localhost:8000', $clone->getHeaderLine('Host'));
        $this->assertSame('www1.localhost', $subject->getHeaderLine('Host'));
    }

    public function testSetUriWithoutHostWithoutPreservationHostHeader(): void
    {
        $uri = new Uri();
        $subject = $this->createSubject()->withHeader('Host', 'www1.localhost');
        $clone = $subject->withUri($uri, false);

        $this->assertNotSame($clone, $subject);
        $this->assertSame('www1.localhost', $clone->getHeaderLine('Host'));
        $this->assertSame('www1.localhost', $subject->getHeaderLine('Host'));
    }

    public function testSetUriWithDefaultBehaviourForPreservationHostHeader(): void
    {
        $uri = new Uri('//www2.localhost');
        $subject = $this->createSubject()->withHeader('Host', 'www1.localhost');
        $clone = $subject->withUri($uri, /* must be false */);

        $this->assertNotSame($clone, $subject);
        $this->assertSame('www2.localhost', $clone->getHeaderLine('Host'));
        $this->assertSame('www1.localhost', $subject->getHeaderLine('Host'));
    }

    public function testSetRequestTargetAsAbsoluteForm(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withRequestTarget('http://localhost/path?query');

        $this->assertNotSame($clone, $subject);
        $this->assertSame('http://localhost/path?query', $clone->getRequestTarget());
        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testSetRequestTargetAsAuthorityForm(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withRequestTarget('localhost:3000');

        $this->assertNotSame($clone, $subject);
        $this->assertSame('localhost:3000', $clone->getRequestTarget());
        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testSetRequestTargetAsOriginForm(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withRequestTarget('/path?query');

        $this->assertNotSame($clone, $subject);
        $this->assertSame('/path?query', $clone->getRequestTarget());
        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testSetRequestTargetAsAsteriskForm(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withRequestTarget('*');

        $this->assertNotSame($clone, $subject);
        $this->assertSame('*', $clone->getRequestTarget());
        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testSetRequestTargetAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP request target must be a string');

        $this->createSubject()->withRequestTarget(null);
    }

    public function testSetRequestTargetAsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP request target cannot be an empty');

        $this->createSubject()->withRequestTarget('');
    }

    public function testSetInvalidRequestTarget(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP request target');

        $this->createSubject()->withRequestTarget("/\0");
    }

    public function testGetRequestTargetByFullUri(): void
    {
        $uri = new Uri('http://user:password@localhost:8000/path?query#fragment');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/path?query', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithPathAndQueryOnly(): void
    {
        $uri = new Uri('/path?query');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/path?query', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithPathOnly(): void
    {
        $uri = new Uri('/path');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/path', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithQueryOnly(): void
    {
        $uri = new Uri('?query');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithRelativePathOnly(): void
    {
        $uri = new Uri('path');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithPathThatContainsTwoLeadingSlashes(): void
    {
        $uri = new Uri('//localhost//path');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/path', $subject->getRequestTarget());
    }

    public function testGetRequestTargetByUriWithPathThatContainsThreeLeadingSlashes(): void
    {
        $uri = new Uri('//localhost///path');

        $subject = $this->createSubject()->withUri($uri);

        $this->assertSame('/path', $subject->getRequestTarget());
    }

    public function testGetRequestTargetIgnoringNewUri(): void
    {
        $subject = $this->createSubject()
            ->withRequestTarget('/foo')
            ->withUri(new Uri('/bar'));

        $this->assertSame('/foo', $subject->getRequestTarget());
    }

    public function testConstructorWithMethod(): void
    {
        $subject = $this->createSubjectWithMethod('POST');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame('POST', $subject->getMethod());
    }

    public function testConstructorWithLowerCaseMethod(): void
    {
        $subject = $this->createSubjectWithMethod('post');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame('post', $subject->getMethod());
    }

    public function testConstructorWithNonStandardMethod(): void
    {
        $subject = $this->createSubjectWithMethod('CUSTOM');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame('CUSTOM', $subject->getMethod());
    }

    public function testConstructorWithEmptyMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method cannot be an empty');

        $subject = $this->createSubjectWithMethod('');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithInvalidMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method');

        $subject = $this->createSubjectWithMethod("GET\0");

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithUri(): void
    {
        $uri = new Uri('//foo/bar');
        $subject = $this->createSubjectWithUri($uri);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame($uri, $subject->getUri());
        $this->assertSame('foo', $subject->getHeaderLine('Host'));
        $this->assertSame('/bar', $subject->getRequestTarget());
    }

    public function testConstructorWithStringUri(): void
    {
        $subject = $this->createSubjectWithUri('//foo/bar');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame('//foo/bar', $subject->getUri()->__toString());
        $this->assertSame('foo', $subject->getHeaderLine('Host'));
        $this->assertSame('/bar', $subject->getRequestTarget());
    }

    public function testConstructorWithInvalidUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URI');

        $subject = $this->createSubjectWithUri(':');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }
}
