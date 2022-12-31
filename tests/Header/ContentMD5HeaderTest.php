<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\ContentMD5Header;

class ContentMD5HeaderTest extends TestCase
{
    public const TEST_MD5_DIGEST = 'YzRjYTQyMzhhMGI5MjM4MjBkY2M1MDlhNmY3NTg0OWI=';

    public function testContracts()
    {
        $header = new ContentMD5Header(self::TEST_MD5_DIGEST);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new ContentMD5Header(self::TEST_MD5_DIGEST);

        $this->assertSame('Content-MD5', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new ContentMD5Header(self::TEST_MD5_DIGEST);

        $this->assertSame(self::TEST_MD5_DIGEST, $header->getFieldValue());
    }

    public function testEmptyValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The value "" for the header "Content-MD5" is not valid'
        );

        new ContentMD5Header('');
    }

    public function testInvalidValue()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(
            'The value "=invalid md5 digest=" for the header "Content-MD5" is not valid'
        );

        new ContentMD5Header('=invalid md5 digest=');
    }

    public function testBuild()
    {
        $header = new ContentMD5Header(self::TEST_MD5_DIGEST);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new ContentMD5Header(self::TEST_MD5_DIGEST);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
