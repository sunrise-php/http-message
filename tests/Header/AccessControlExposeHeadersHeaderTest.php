<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlExposeHeadersHeader;

class AccessControlExposeHeadersHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo');

        $this->assertSame('Access-Control-Expose-Headers', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo');

        $this->assertSame('x-foo', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo', 'x-bar', 'x-baz');

        $this->assertSame('x-foo, x-bar, x-baz', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Expose-Headers" is not valid');

        new AccessControlExposeHeadersHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Expose-Headers" is not valid');

        new AccessControlExposeHeadersHeader('x-foo', '', 'x-baz');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Access-Control-Expose-Headers" is not valid');

        // isn't a token...
        new AccessControlExposeHeadersHeader('@');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Access-Control-Expose-Headers" is not valid');

        // isn't a token...
        new AccessControlExposeHeadersHeader('x-foo', '@', 'x-baz');
    }

    public function testBuild()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AccessControlExposeHeadersHeader('x-foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
