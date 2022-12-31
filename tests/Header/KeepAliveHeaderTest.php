<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\KeepAliveHeader;

class KeepAliveHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new KeepAliveHeader();

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new KeepAliveHeader();

        $this->assertSame('Keep-Alive', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new KeepAliveHeader();

        $this->assertSame('', $header->getFieldValue());
    }

    public function testParameterWithEmptyValue()
    {
        $header = new KeepAliveHeader([
            'foo' => '',
        ]);

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testParameterWithToken()
    {
        $header = new KeepAliveHeader([
            'foo' => 'token',
        ]);

        $this->assertSame('foo=token', $header->getFieldValue());
    }

    public function testParameterWithQuotedString()
    {
        $header = new KeepAliveHeader([
            'foo' => 'quoted string',
        ]);

        $this->assertSame('foo="quoted string"', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $header = new KeepAliveHeader([
            'foo' => 1,
        ]);

        $this->assertSame('foo=1', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $header = new KeepAliveHeader([
            'foo' => '',
            'bar' => 'token',
            'baz' => 'quoted string',
            'qux' => 1,
        ]);

        $this->assertSame('foo, bar=token, baz="quoted string", qux=1', $header->getFieldValue());
    }

    public function testInvalidParameterName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "invalid name" for the header "Keep-Alive" is not valid'
        );

        // cannot contain spaces...
        new KeepAliveHeader(['invalid name' => 'value']);
    }

    public function testInvalidParameterNameType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "Keep-Alive" is not valid'
        );

        new KeepAliveHeader([0 => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ""invalid value"" for the header "Keep-Alive" is not valid'
        );

        // cannot contain quotes...
        new KeepAliveHeader(['name' => '"invalid value"']);
    }

    public function testInvalidParameterValueType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "Keep-Alive" is not valid'
        );

        new KeepAliveHeader(['name' => []]);
    }

    public function testBuild()
    {
        $header = new KeepAliveHeader(['foo' => 'bar']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new KeepAliveHeader(['foo' => 'bar']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
