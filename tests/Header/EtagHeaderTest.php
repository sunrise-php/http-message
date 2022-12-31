<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\EtagHeader;

class EtagHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new EtagHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new EtagHeader('foo');

        $this->assertSame('ETag', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new EtagHeader('foo');

        $this->assertSame('"foo"', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $header = new EtagHeader('');

        $this->assertSame('""', $header->getFieldValue());
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value ""invalid value"" for the header "ETag" is not valid');

        // cannot contain quotes...
        new EtagHeader('"invalid value"');
    }

    public function testBuild()
    {
        $header = new EtagHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new EtagHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
