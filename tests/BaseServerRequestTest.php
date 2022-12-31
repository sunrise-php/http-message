<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

abstract class BaseServerRequestTest extends BaseRequestTest
{
    abstract protected function createSubject(): ServerRequestInterface;

    protected function createSubjectWithServerParams(array $serverParams): ?ServerRequestInterface
    {
        return null;
    }

    protected function createSubjectWithQueryParams(array $queryParams): ?ServerRequestInterface
    {
        return null;
    }

    protected function createSubjectWithCookieParams(array $cookieParams): ?ServerRequestInterface
    {
        return null;
    }

    protected function createSubjectWithUploadedFiles(array $uploadedFiles): ?ServerRequestInterface
    {
        return null;
    }

    protected function createSubjectWithParsedBody($parsedBody): ?ServerRequestInterface
    {
        return null;
    }

    protected function createSubjectWithAttributes(array $attributes): ?ServerRequestInterface
    {
        return null;
    }

    public function testDefaultServerParams(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getServerParams());
    }

    public function testDefaultQueryParams(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getQueryParams());
    }

    public function testDefaultCookieParams(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getCookieParams());
    }

    public function testDefaultUploadedFiles(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getUploadedFiles());
    }

    public function testDefaultParsedBody(): void
    {
        $subject = $this->createSubject();

        $this->assertNull($subject->getParsedBody());
    }

    public function testDefaultAttributes(): void
    {
        $subject = $this->createSubject();

        $this->assertSame([], $subject->getAttributes());
    }

    public function testSetQueryParams(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withQueryParams(['foo' => 'bar']);

        $this->assertNotSame($clone, $subject);
        $this->assertSame(['foo' => 'bar'], $clone->getQueryParams());
        $this->assertSame([], $subject->getQueryParams());
    }

    public function testSetEmptyQueryParams(): void
    {
        $this->assertSame([], $this->createSubject()
            ->withQueryParams(['foo' => 'bar'])
            ->withQueryParams([])
            ->getQueryParams());
    }

    public function testSetCookieParams(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withCookieParams(['foo' => 'bar']);

        $this->assertNotSame($clone, $subject);
        $this->assertSame(['foo' => 'bar'], $clone->getCookieParams());
        $this->assertSame([], $subject->getCookieParams());
    }

    public function testSetEmptyCookieParams(): void
    {
        $this->assertSame([], $this->createSubject()
            ->withCookieParams(['foo' => 'bar'])
            ->withCookieParams([])
            ->getCookieParams());
    }

    public function testSetUploadedFiles(): void
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $subject = $this->createSubject();
        $clone = $subject->withUploadedFiles(['foo' => $file]);

        $this->assertNotSame($clone, $subject);
        $this->assertSame(['foo' => $file], $clone->getUploadedFiles());
        $this->assertSame([], $subject->getUploadedFiles());
    }

    public function testSetEmptyUploadedFiles(): void
    {
        $this->assertSame([], $this->createSubject()
            ->withUploadedFiles([
                'foo' => $this->createMock(UploadedFileInterface::class),
            ])
            ->withUploadedFiles([])
            ->getUploadedFiles());
    }

    public function testSetInvalidUploadedFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid uploaded files');

        $this->createSubject()->withUploadedFiles(['foo' => 'bar']);
    }

    public function testSetParsedBodyAsNull(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withParsedBody(null);

        $this->assertNotSame($clone, $subject);
        $this->assertNull($clone->getParsedBody());
        $this->assertNull($subject->getParsedBody());
    }

    public function testSetParsedBodyAsArray(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withParsedBody(['foo' => 'bar']);

        $this->assertNotSame($clone, $subject);
        $this->assertSame(['foo' => 'bar'], $clone->getParsedBody());
        $this->assertNull($subject->getParsedBody());
    }

    public function testSetParsedBodyAsEmptyArray(): void
    {
        $this->assertSame([], $this->createSubject()
            ->withParsedBody(['foo' => 'bar'])
            ->withParsedBody([])
            ->getParsedBody());
    }

    public function testSetParsedBodyAsObject(): void
    {
        $object = new \stdClass;
        $subject = $this->createSubject();
        $clone = $subject->withParsedBody($object);

        $this->assertNotSame($clone, $subject);
        $this->assertSame($object, $clone->getParsedBody());
        $this->assertNull($subject->getParsedBody());
    }

    public function testSetInvalidParsedBody(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parsed body');

        $this->createSubject()->withParsedBody('foo');
    }

    public function testSetAttribute(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withAttribute('foo', 'bar');

        $this->assertNotSame($clone, $subject);
        $this->assertSame(['foo' => 'bar'], $clone->getAttributes());
        $this->assertSame([], $subject->getAttributes());
    }

    public function testGetAttribute(): void
    {
        $this->assertSame('bar', $this->createSubject()
                ->withAttribute('foo', 'bar')
                ->getAttribute('foo'));
    }

    public function testGetUnknownAttribute(): void
    {
        $this->assertNull($this->createSubject()
                ->getAttribute('foo'));
    }

    public function testGetUnknownAttributeWithDefaultValue(): void
    {
        $this->assertFalse($this->createSubject()
                ->getAttribute('foo', false));
    }

    public function testReplaceAttribute(): void
    {
        $this->assertSame('baz', $this->createSubject()
            ->withAttribute('foo', 'bar')
            ->withAttribute('foo', 'baz')
            ->getAttribute('foo'));
    }

    public function testDeleteAttribute(): void
    {
        $subject = $this->createSubject()
            ->withAttribute('foo', 'bar');

        $clone = $subject->withoutAttribute('foo');

        $this->assertNotSame($clone, $subject);
        $this->assertSame([], $clone->getAttributes());
        $this->assertSame(['foo' => 'bar'], $subject->getAttributes());
    }

    public function testConstructorWithServerParams(): void
    {
        $subject = $this->createSubjectWithServerParams(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => 'bar'], $subject->getServerParams());
    }

    public function testConstructorWithQueryParams(): void
    {
        $subject = $this->createSubjectWithQueryParams(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => 'bar'], $subject->getQueryParams());
    }

    public function testConstructorWithCookieParams(): void
    {
        $subject = $this->createSubjectWithCookieParams(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => 'bar'], $subject->getCookieParams());
    }

    public function testConstructorWithUploadedFiles(): void
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $subject = $this->createSubjectWithUploadedFiles(['foo' => $file]);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => $file], $subject->getUploadedFiles());
    }

    public function testConstructorWithInvalidUploadedFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid uploaded files');

        $subject = $this->createSubjectWithUploadedFiles(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithParsedBodyAsNull(): void
    {
        $subject = $this->createSubjectWithParsedBody(null);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertNull($subject->getParsedBody());
    }

    public function testConstructorWithParsedBodyAsArray(): void
    {
        $subject = $this->createSubjectWithParsedBody(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => 'bar'], $subject->getParsedBody());
    }

    public function testConstructorWithParsedBodyAsObject(): void
    {
        $object = new \stdClass;
        $subject = $this->createSubjectWithParsedBody($object);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame($object, $subject->getParsedBody());
    }

    public function testConstructorWithInvalidParsedBody(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parsed body');

        $subject = $this->createSubjectWithParsedBody('foo');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithAttributes(): void
    {
        $subject = $this->createSubjectWithAttributes(['foo' => 'bar']);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(['foo' => 'bar'], $subject->getAttributes());
    }
}
