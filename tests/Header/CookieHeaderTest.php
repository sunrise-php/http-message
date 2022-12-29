<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\CookieHeader;

class CookieHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new CookieHeader();

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new CookieHeader();

        $this->assertSame('Cookie', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new CookieHeader([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => [
                'qux',
            ],
        ]);

        $this->assertSame('foo=bar; bar=baz; baz%5B0%5D=qux', $header->getFieldValue());
    }

    public function testBuild()
    {
        $header = new CookieHeader(['foo' => 'bar']);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new CookieHeader(['foo' => 'bar']);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
