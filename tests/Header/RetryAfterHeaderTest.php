<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\RetryAfterHeader;

class RetryAfterHeaderTest extends TestCase
{
    public function testContracts()
    {
        $utc = new \DateTime('utc');
        $header = new RetryAfterHeader($utc);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $utc = new \DateTime('utc');
        $header = new RetryAfterHeader($utc);

        $this->assertSame('Retry-After', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $utc = new \DateTime('utc');
        $header = new RetryAfterHeader($utc);

        $this->assertSame($utc->format(\DateTime::RFC822), $header->getFieldValue());
    }

    public function testFieldValueWithMutableDateTime()
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $utc = new \DateTime('now', new \DateTimeZone('UTC'));

        $header = new RetryAfterHeader($now);

        $this->assertSame($utc->format(\DateTime::RFC822), $header->getFieldValue());
        $this->assertSame('Europe/Moscow', $now->getTimezone()->getName());
    }

    public function testFieldValueWithImmutableDateTime()
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));
        $utc = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $header = new RetryAfterHeader($now);

        $this->assertSame($utc->format(\DateTimeImmutable::RFC822), $header->getFieldValue());
        $this->assertSame('Europe/Moscow', $now->getTimezone()->getName());
    }

    public function testBuild()
    {
        $now = new \DateTime('now');
        $header = new RetryAfterHeader($now);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $now = new \DateTime('now');
        $header = new RetryAfterHeader($now);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}