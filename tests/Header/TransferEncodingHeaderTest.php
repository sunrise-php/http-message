<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\TransferEncodingHeader;

class TransferEncodingHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('chunked', TransferEncodingHeader::CHUNKED);
        $this->assertSame('compress', TransferEncodingHeader::COMPRESS);
        $this->assertSame('deflate', TransferEncodingHeader::DEFLATE);
        $this->assertSame('gzip', TransferEncodingHeader::GZIP);
    }

    public function testContracts()
    {
        $header = new TransferEncodingHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new TransferEncodingHeader('foo');

        $this->assertSame('Transfer-Encoding', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new TransferEncodingHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new TransferEncodingHeader('foo', 'bar', 'baz');

        $this->assertSame('foo, bar, baz', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Transfer-Encoding" is not valid');

        new TransferEncodingHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Transfer-Encoding" is not valid');

        new TransferEncodingHeader('foo', '', 'baz');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Transfer-Encoding" is not valid');

        // isn't a token...
        new TransferEncodingHeader('@');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Transfer-Encoding" is not valid');

        // isn't a token...
        new TransferEncodingHeader('foo', '@', 'baz');
    }

    public function testBuild()
    {
        $header = new TransferEncodingHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new TransferEncodingHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
