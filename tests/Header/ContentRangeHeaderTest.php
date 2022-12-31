<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentRangeHeader;

class ContentRangeHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ContentRangeHeader(0, 1, 2);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentRangeHeader(0, 1, 2);

        $this->assertSame('Content-Range', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentRangeHeader(0, 1, 2);

        $this->assertSame('bytes 0-1/2', $header->getFieldValue());
    }

    public function testInvalidFirstBytePosition()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The "first-byte-pos" value of the content range ' .
            'must be less than or equal to the "last-byte-pos" value'
        );

        new ContentRangeHeader(2, 1, 2);
    }

    public function testInvalidLastBytePosition()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The "last-byte-pos" value of the content range ' .
            'must be less than the "instance-length" value'
        );

        new ContentRangeHeader(0, 2, 2);
    }

    public function testInvalidInstanceLength()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The "last-byte-pos" value of the content range ' .
            'must be less than the "instance-length" value'
        );

        new ContentRangeHeader(0, 1, 1);
    }

    public function testBuild()
    {
        $header = new ContentRangeHeader(0, 1, 2);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentRangeHeader(0, 1, 2);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
