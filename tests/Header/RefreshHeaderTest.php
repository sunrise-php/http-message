<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\RefreshHeader;
use Sunrise\Http\Message\Uri;

class RefreshHeaderTest extends TestCase
{
    public function testContracts()
    {
        $uri = new Uri('/');
        $header = new RefreshHeader(0, $uri);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $uri = new Uri('/');
        $header = new RefreshHeader(0, $uri);

        $this->assertSame('Refresh', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $uri = new Uri('/');
        $header = new RefreshHeader(0, $uri);

        $this->assertSame('0; url=/', $header->getFieldValue());
    }

    public function testInvalidDelay()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The delay "-1" for the header "Refresh" is not valid');

        $uri = new Uri('/');

        new RefreshHeader(-1, $uri);
    }

    public function testBuild()
    {
        $uri = new Uri('/');
        $header = new RefreshHeader(0, $uri);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $uri = new Uri('/');
        $header = new RefreshHeader(0, $uri);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
