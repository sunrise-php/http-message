<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\CacheControlHeader;

class CacheControlHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new CacheControlHeader([]);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new CacheControlHeader([]);

        $this->assertSame('Cache-Control', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new CacheControlHeader([]);

        $this->assertSame('', $header->getFieldValue());
    }

    public function testParameterWithEmptyValue()
    {
        $header = new CacheControlHeader([
            'foo' => '',
        ]);

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testParameterWithToken()
    {
        $header = new CacheControlHeader([
            'foo' => 'token',
        ]);

        $this->assertSame('foo=token', $header->getFieldValue());
    }

    public function testParameterWithQuotedString()
    {
        $header = new CacheControlHeader([
            'foo' => 'quoted string',
        ]);

        $this->assertSame('foo="quoted string"', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $header = new CacheControlHeader([
            'foo' => 1,
        ]);

        $this->assertSame('foo=1', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $header = new CacheControlHeader([
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
            'The parameter name "invalid name" for the header "Cache-Control" is not valid'
        );

        new CacheControlHeader(['invalid name' => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ""invalid value"" for the header "Cache-Control" is not valid'
        );

        new CacheControlHeader(['name' => '"invalid value"']);
    }

    public function testInvalidParameterNameType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "Cache-Control" is not valid'
        );

        new CacheControlHeader([0 => 'value']);
    }

    public function testInvalidParameterValueType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "Cache-Control" is not valid'
        );

        new CacheControlHeader(['name' => []]);
    }

    public function testBuild()
    {
        $header = new CacheControlHeader(['foo' => 'bar']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new CacheControlHeader(['foo' => 'bar']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
