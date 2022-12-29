<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ClearSiteDataHeader;

class ClearSiteDataHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ClearSiteDataHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ClearSiteDataHeader('foo');

        $this->assertSame('Clear-Site-Data', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ClearSiteDataHeader('foo');

        $this->assertSame('"foo"', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new ClearSiteDataHeader('foo', 'bar', 'baz');

        $this->assertSame('"foo", "bar", "baz"', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $header = new ClearSiteDataHeader('');

        $this->assertSame('""', $header->getFieldValue());
    }

    public function testEmptyValueAmongOthers()
    {
        $header = new ClearSiteDataHeader('foo', '', 'baz');

        $this->assertSame('"foo", "", "baz"', $header->getFieldValue());
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value ""invalid value"" for the header "Clear-Site-Data" is not valid');

        // cannot contain quotes...
        new ClearSiteDataHeader('"invalid value"');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value ""bar"" for the header "Clear-Site-Data" is not valid');

        // cannot contain quotes...
        new ClearSiteDataHeader('foo', '"bar"', 'baz');
    }

    public function testBuild()
    {
        $header = new ClearSiteDataHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ClearSiteDataHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
