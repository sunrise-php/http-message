<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\AgeHeader;

class AgeHeaderTest extends TestCase
{
    public function testContracts()
    {
        $header = new AgeHeader(0);

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new AgeHeader(0);

        $this->assertSame('Age', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new AgeHeader(0);

        $this->assertSame('0', $header->getFieldValue());
    }

    /**
     * @dataProvider validValueDataProvider
     */
    public function testValidValue(int $validValue)
    {
        $header = new AgeHeader($validValue);

        $this->assertEquals($validValue, $header->getFieldValue());
    }

    public function validValueDataProvider(): array
    {
        return [[0], [1], [2]];
    }

    /**
     * @dataProvider invalidValueDataProvider
     */
    public function testInvalidValue(int $invalidValue)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->expectExceptionMessage(sprintf(
            'The value "%d" for the header "Age" is not valid',
            $invalidValue
        ));

        new AgeHeader($invalidValue);
    }

    public function invalidValueDataProvider(): array
    {
        return [[-3], [-2], [-1]];
    }

    public function testBuild()
    {
        $header = new AgeHeader(0);

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new AgeHeader(0);

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
