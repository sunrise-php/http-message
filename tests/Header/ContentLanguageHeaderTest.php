<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentLanguageHeader;

class ContentLanguageHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new ContentLanguageHeader('foo');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentLanguageHeader('foo');

        $this->assertSame('Content-Language', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentLanguageHeader('foo');

        $this->assertSame('foo', $header->getFieldValue());
    }

    public function testSeveralValues()
    {
        $header = new ContentLanguageHeader('foo', 'bar', 'baz');

        $this->assertSame('foo, bar, baz', $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Content-Language" is not valid');

        new ContentLanguageHeader('');
    }

    public function testEmptyValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Content-Language" is not valid');

        new ContentLanguageHeader('foo', '', 'baz');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Content-Language" is not valid');

        // isn't a token...
        new ContentLanguageHeader('@');
    }

    public function testInvalidValueAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Content-Language" is not valid');

        // isn't a token...
        new ContentLanguageHeader('foo', '@', 'baz');
    }

    public function testInvalidValueLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "VERYLONGWORD" for the header "Content-Language" is not valid');

        // isn't a token...
        new ContentLanguageHeader('VERYLONGWORD');
    }

    public function testInvalidValueLengthAmongOthers()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "VERYLONGWORD" for the header "Content-Language" is not valid');

        // isn't a token...
        new ContentLanguageHeader('foo', 'VERYLONGWORD', 'baz');
    }

    public function testBuild()
    {
        $header = new ContentLanguageHeader('foo');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentLanguageHeader('foo');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
