<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentTypeHeader;

class ContentTypeHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ContentTypeHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentTypeHeader('foo');

        $this->assertSame('Content-Type', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentTypeHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testParameterWithEmptyValue()
    {
        $header = new ContentTypeHeader('foo', [
            'bar' => '',
        ]);

        $this->assertSame('foo; bar=""', $header->getFieldValue());
    }

    public function testParameterWithToken()
    {
        $header = new ContentTypeHeader('foo', [
            'bar' => 'token',
        ]);

        $this->assertSame('foo; bar="token"', $header->getFieldValue());
    }

    public function testParameterWithQuotedString()
    {
        $header = new ContentTypeHeader('foo', [
            'bar' => 'quoted string',
        ]);

        $this->assertSame('foo; bar="quoted string"', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $header = new ContentTypeHeader('foo', [
            'bar' => 1,
        ]);

        $this->assertSame('foo; bar="1"', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $header = new ContentTypeHeader('foo', [
            'bar' => '',
            'baz' => 'token',
            'bat' => 'quoted string',
            'qux' => 1,
        ]);

        $this->assertSame('foo; bar=""; baz="token"; bat="quoted string"; qux="1"', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Content-Type" is not valid');

        new ContentTypeHeader('');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Content-Type" is not valid');

        // isn't a token...
        new ContentTypeHeader('@');
    }

    public function testInvalidParameterName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "invalid name" for the header "Content-Type" is not valid'
        );

        // cannot contain spaces...
        new ContentTypeHeader('foo', ['invalid name' => 'value']);
    }

    public function testInvalidParameterNameType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "Content-Type" is not valid'
        );

        // cannot contain spaces...
        new ContentTypeHeader('foo', [0 => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ""invalid value"" for the header "Content-Type" is not valid'
        );

        // cannot contain quotes...
        new ContentTypeHeader('foo', ['name' => '"invalid value"']);
    }

    public function testInvalidParameterValueType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "Content-Type" is not valid'
        );

        // cannot contain quotes...
        new ContentTypeHeader('foo', ['name' => []]);
    }

    public function testBuild()
    {
        $header = new ContentTypeHeader('foo', ['bar' => 'baz']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentTypeHeader('foo', ['bar' => 'baz']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
