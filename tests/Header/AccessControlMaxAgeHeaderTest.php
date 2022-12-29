<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AccessControlMaxAgeHeader;

class AccessControlMaxAgeHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AccessControlMaxAgeHeader(-1);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AccessControlMaxAgeHeader(-1);

        $this->assertSame('Access-Control-Max-Age', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AccessControlMaxAgeHeader(-1);

        $this->assertSame('-1', $header->getFieldValue());
    }

    /**
     * @dataProvider validValueDataProvider
     */
    public function testValidValue(int $validValue)
    {
        $header = new AccessControlMaxAgeHeader($validValue);

        $this->assertEquals($validValue, $header->getFieldValue());
    }

    public function validValueDataProvider(): array
    {
        return [[-1], [1], [2]];
    }

    /**
     * @dataProvider invalidValueDataProvider
     */
    public function testInvalidValue(int $invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(sprintf(
            'The value "%d" for the header "Access-Control-Max-Age" is not valid',
            $invalidValue
        ));

        new AccessControlMaxAgeHeader($invalidValue);
    }

    public function invalidValueDataProvider(): array
    {
        return [[-3], [-2], [0]];
    }

    public function testBuild()
    {
        $header = new AccessControlMaxAgeHeader(-1);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AccessControlMaxAgeHeader(-1);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
