<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentLengthHeader;

class ContentLengthHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ContentLengthHeader(0);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentLengthHeader(0);

        $this->assertSame('Content-Length', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentLengthHeader(0);

        $this->assertSame('0', $header->getFieldValue());
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "-1" for the header "Content-Length" is not valid');

        new ContentLengthHeader(-1);
    }

    public function testBuild()
    {
        $header = new ContentLengthHeader(0);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentLengthHeader(0);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
