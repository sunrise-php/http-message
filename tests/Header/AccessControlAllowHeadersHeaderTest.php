<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlAllowHeadersHeader;

class AccessControlAllowHeadersHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo');

        $this->assertSame('Access-Control-Allow-Headers', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo');

        $this->assertSame('x-foo', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo', 'x-bar', 'x-baz');

        $this->assertSame('x-foo, x-bar, x-baz', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Allow-Headers" is not valid');

        new AccessControlAllowHeadersHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Access-Control-Allow-Headers" is not valid');

        new AccessControlAllowHeadersHeader('x-foo', '', 'x-bar');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "x-foo=" for the header "Access-Control-Allow-Headers" is not valid');

        // a token cannot contain the "=" character...
        new AccessControlAllowHeadersHeader('x-foo=');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "x-bar=" for the header "Access-Control-Allow-Headers" is not valid');

        // a token cannot contain the "=" character...
        new AccessControlAllowHeadersHeader('x-foo', 'x-bar=', 'x-bar');
    }

    public function testBuild()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AccessControlAllowHeadersHeader('x-foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
