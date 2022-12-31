<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ConnectionHeader;

class ConnectionHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame('close', ConnectionHeader::CONNECTION_CLOSE);
        $this->assertSame('keep-alive', ConnectionHeader::CONNECTION_KEEP_ALIVE);
    }

    public function testContracts()
    {
        $header = new ConnectionHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ConnectionHeader('foo');

        $this->assertSame('Connection', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ConnectionHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Connection" is not valid');

        new ConnectionHeader('');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Connection" is not valid');

        // isn't a token...
        new ConnectionHeader('@');
    }

    public function testBuild()
    {
        $header = new ConnectionHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ConnectionHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
