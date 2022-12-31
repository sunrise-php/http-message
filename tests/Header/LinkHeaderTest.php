<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\LinkHeader;
use Sunrise\Http\Message\Uri;

class LinkHeaderTest extends TestCase
{
    public function testContracts()
    {
        $uri = new Uri('/');
        $header = new LinkHeader($uri);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $uri = new Uri('/');
        $header = new LinkHeader($uri);

        $this->assertSame('Link', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $uri = new Uri('/');
        $header = new LinkHeader($uri);

        $this->assertSame('</>', $header->getFieldValue());
    }

    public function testParameterWithEmptyValue()
    {
        $uri = new Uri('/');

        $header = new LinkHeader($uri, [
            'foo' => '',
        ]);

        $this->assertSame('</>; foo=""', $header->getFieldValue());
    }

    public function testParameterWithToken()
    {
        $uri = new Uri('/');

        $header = new LinkHeader($uri, [
            'foo' => 'token',
        ]);

        $this->assertSame('</>; foo="token"', $header->getFieldValue());
    }

    public function testParameterWithQuotedString()
    {
        $uri = new Uri('/');

        $header = new LinkHeader($uri, [
            'foo' => 'quoted string',
        ]);

        $this->assertSame('</>; foo="quoted string"', $header->getFieldValue());
    }

    public function testParameterWithInteger()
    {
        $uri = new Uri('/');

        $header = new LinkHeader($uri, [
            'foo' => 1,
        ]);

        $this->assertSame('</>; foo="1"', $header->getFieldValue());
    }

    public function testSeveralParameters()
    {
        $uri = new Uri('/');

        $header = new LinkHeader($uri, [
            'foo' => '',
            'bar' => 'token',
            'baz' => 'quoted string',
            'qux' => 1,
        ]);

        $this->assertSame('</>; foo=""; bar="token"; baz="quoted string"; qux="1"', $header->getFieldValue());
    }

    public function testInvalidParameterName()
    {
        $uri = new Uri('/');

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "invalid name" for the header "Link" is not valid'
        );

        // cannot contain spaces...
        new LinkHeader($uri, ['invalid name' => 'value']);
    }

    public function testInvalidParameterNameType()
    {
        $uri = new Uri('/');

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter name "<integer>" for the header "Link" is not valid'
        );

        new LinkHeader($uri, [0 => 'value']);
    }

    public function testInvalidParameterValue()
    {
        $uri = new Uri('/');

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value ""invalid value"" for the header "Link" is not valid'
        );

        // cannot contain quotes...
        new LinkHeader($uri, ['name' => '"invalid value"']);
    }

    public function testInvalidParameterValueType()
    {
        $uri = new Uri('/');

        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The parameter value "<array>" for the header "Link" is not valid'
        );

        // cannot contain quotes...
        new LinkHeader($uri, ['name' => []]);
    }

    public function testBuild()
    {
        $uri = new Uri('/');
        $header = new LinkHeader($uri, ['foo' => 'bar']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $uri = new Uri('/');
        $header = new LinkHeader($uri, ['foo' => 'bar']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
