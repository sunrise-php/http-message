<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentEncodingHeader;

class ContentEncodingHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('gzip', ContentEncodingHeader::GZIP);
        $this->assertSame('compress', ContentEncodingHeader::COMPRESS);
        $this->assertSame('deflate', ContentEncodingHeader::DEFLATE);
        $this->assertSame('br', ContentEncodingHeader::BR);
    }

    public function testContracts()
    {
        $header = new ContentEncodingHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentEncodingHeader('foo');

        $this->assertSame('Content-Encoding', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentEncodingHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new ContentEncodingHeader('foo', 'bar', 'baz');

        $this->assertSame('foo, bar, baz', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Content-Encoding" is not valid');

        new ContentEncodingHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Content-Encoding" is not valid');

        new ContentEncodingHeader('foo', '', 'bar');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "foo=" for the header "Content-Encoding" is not valid');

        // a token cannot contain the "=" character...
        new ContentEncodingHeader('foo=');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "bar=" for the header "Content-Encoding" is not valid');

        // a token cannot contain the "=" character...
        new ContentEncodingHeader('foo', 'bar=', 'bar');
    }

    public function testBuild()
    {
        $header = new ContentEncodingHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentEncodingHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
