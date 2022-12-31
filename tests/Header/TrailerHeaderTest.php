<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\TrailerHeader;

class TrailerHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new TrailerHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new TrailerHeader('foo');

        $this->assertSame('Trailer', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new TrailerHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The value "" for the header "Trailer" is not valid'
        );

        new TrailerHeader('');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The value "@" for the header "Trailer" is not valid'
        );

        // isn't a token...
        new TrailerHeader('@');
    }

    public function testBuild()
    {
        $header = new TrailerHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new TrailerHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
