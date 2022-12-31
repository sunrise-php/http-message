<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Message\Exception\InvalidArgumentException;

abstract class BaseResponseTest extends BaseMessageTest
{
    abstract protected function createSubject(): ResponseInterface;

    protected function createSubjectWithStatus(int $statusCode, string $reasonPhrase = ''): ?ResponseInterface
    {
        return null;
    }

    public function testContracts(): void
    {
        $subject = $this->createSubject();

        $this->assertInstanceOf(StatusCodeInterface::class, $subject);
    }

    public function testDefaultStatusCode(): void
    {
        $subject = $this->createSubject();

        $this->assertSame(200, $subject->getStatusCode());
    }

    public function testDefaultReasonPhrase(): void
    {
        $subject = $this->createSubject();

        $this->assertSame('OK', $subject->getReasonPhrase());
    }

    public function testSetStatusCode(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withStatus(202);

        $this->assertNotSame($clone, $subject);

        $this->assertSame(202, $clone->getStatusCode());
        $this->assertSame('Accepted', $clone->getReasonPhrase());

        $this->assertSame(200, $subject->getStatusCode());
        $this->assertSame('OK', $subject->getReasonPhrase());
    }

    public function testSetStatusCodeWithReasonPhrase(): void
    {
        $subject = $this->createSubject();
        $clone = $subject->withStatus(202, 'Custom Reason Phrase');

        $this->assertNotSame($clone, $subject);

        $this->assertSame(202, $clone->getStatusCode());
        $this->assertSame('Custom Reason Phrase', $clone->getReasonPhrase());

        $this->assertSame(200, $subject->getStatusCode());
        $this->assertSame('OK', $subject->getReasonPhrase());
    }

    public function testSetStatusCodeWithEmptyReasonPhrase(): void
    {
        $subject = $this->createSubject()->withStatus(202, '');

        $this->assertSame('Accepted', $subject->getReasonPhrase());
    }

    public function testSetStatusCodeThatHasNoReasonPhrase(): void
    {
        $subject = $this->createSubject()->withStatus(599);

        $this->assertSame('Unknown Status Code', $subject->getReasonPhrase());
    }

    public function testSetStatusCodeAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP status code must be an integer');

        $this->createSubject()->withStatus(null);
    }

    public function testSetStatusCodeAsStringNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP status code must be an integer');

        $this->createSubject()->withStatus('200');
    }

    public function testSetStatusCodeAsNumberLessThan100(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        $this->createSubject()->withStatus(99);
    }

    public function testSetStatusCodeAsNumberGreaterThan599(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        $this->createSubject()->withStatus(600);
    }

    public function testSetReasonPhraseAsNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP reason phrase must be a string');

        $this->createSubject()->withStatus(200, null);
    }

    public function testSetReasonPhraseAsNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP reason phrase must be a string');

        $this->createSubject()->withStatus(200, 42);
    }

    public function testSetInvalidReasonPhrase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP reason phrase');

        $this->createSubject()->withStatus(200, "\0");
    }

    public function testConstructorWithStatusCodeWithoutReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(200);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(200, $subject->getStatusCode());
        $this->assertSame('OK', $subject->getReasonPhrase());
    }

    public function testConstructorWithStatusCodeWithEmptyReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(200, '');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(200, $subject->getStatusCode());
        $this->assertSame('OK', $subject->getReasonPhrase());
    }

    public function testConstructorWithStatusCodeWithCustomReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(200, 'Custom Reason Phrase');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(200, $subject->getStatusCode());
        $this->assertSame('Custom Reason Phrase', $subject->getReasonPhrase());
    }

    public function testConstructorWithUnknownStatusCodeWithoutReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(599);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(599, $subject->getStatusCode());
        $this->assertSame('Unknown Status Code', $subject->getReasonPhrase());
    }

    public function testConstructorWithUnknownStatusCodeWithEmptyReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(599, '');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(599, $subject->getStatusCode());
        $this->assertSame('Unknown Status Code', $subject->getReasonPhrase());
    }

    public function testConstructorWithUnknownStatusCodeWithReasonPhrase(): void
    {
        $subject = $this->createSubjectWithStatus(599, 'Custom Reason Phrase');

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }

        $this->assertSame(599, $subject->getStatusCode());
        $this->assertSame('Custom Reason Phrase', $subject->getReasonPhrase());
    }

    public function testConstructorWithStatusCodeLessThan100(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        $subject = $this->createSubjectWithStatus(99);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithStatusCodeGreaterThan599(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code');

        $subject = $this->createSubjectWithStatus(600);

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }

    public function testConstructorWithInvalidReasonPhrase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP reason phrase');

        $subject = $this->createSubjectWithStatus(200, "\0");

        if (!isset($subject)) {
            $this->markTestSkipped(__FUNCTION__);
        }
    }
}
