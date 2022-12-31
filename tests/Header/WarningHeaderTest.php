<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Header;

use PHPUnit\Framework\TestCase;
use Sunrise\Http\Message\HeaderInterface;
use Sunrise\Http\Message\Header\WarningHeader;

class WarningHeaderTest extends TestCase
{
    public function testConstants()
    {
        $this->assertSame(110, WarningHeader::HTTP_WARNING_CODE_RESPONSE_IS_STALE);
        $this->assertSame(111, WarningHeader::HTTP_WARNING_CODE_REVALIDATION_FAILED);
        $this->assertSame(112, WarningHeader::HTTP_WARNING_CODE_DISCONNECTED_OPERATION);
        $this->assertSame(113, WarningHeader::HTTP_WARNING_CODE_HEURISTIC_EXPIRATION);
        $this->assertSame(199, WarningHeader::HTTP_WARNING_CODE_MISCELLANEOUS_WARNING);
        $this->assertSame(214, WarningHeader::HTTP_WARNING_CODE_TRANSFORMATION_APPLIED);
        $this->assertSame(299, WarningHeader::HTTP_WARNING_CODE_MISCELLANEOUS_PERSISTENT_WARNING);
    }

    public function testContracts()
    {
        $header = new WarningHeader(199, 'agent', 'text');

        $this->assertInstanceOf(HeaderInterface::class, $header);
    }

    public function testFieldName()
    {
        $header = new WarningHeader(199, 'agent', 'text');

        $this->assertSame('Warning', $header->getFieldName());
    }

    public function testFieldValue()
    {
        $header = new WarningHeader(199, 'agent', 'text');

        $this->assertSame('199 agent "text"', $header->getFieldValue());
    }

    public function testFieldValueWithDate()
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $utc = new \DateTime('now', new \DateTimeZone('UTC'));

        $header = new WarningHeader(199, 'agent', 'text', $now);

        $this->assertSame(
            \sprintf(
                '199 agent "text" "%s"',
                $utc->format(\DateTime::RFC822)
            ),
            $header->getFieldValue()
        );

        // cannot be modified...
        $this->assertSame('Europe/Moscow', $now->getTimezone()->getName());
    }

    public function testCodeLessThat100()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The code "99" for the header "Warning" is not valid');

        new WarningHeader(99, 'agent', 'text');
    }

    public function testCodeGreaterThat999()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The code "1000" for the header "Warning" is not valid');

        new WarningHeader(1000, 'agent', 'text');
    }

    public function testEmptyAgent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "" for the header "Warning" is not valid');

        new WarningHeader(199, '', 'text');
    }

    public function testInvalidAgent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value "@" for the header "Warning" is not valid');

        // isn't a token...
        new WarningHeader(199, '@', 'text');
    }

    public function testEmptyText()
    {
        $header = new WarningHeader(199, 'agent', '');

        $this->assertSame('199 agent ""', $header->getFieldValue());
    }

    public function testInvalidText()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value ""text"" for the header "Warning" is not valid');

        // cannot contain quotes...
        new WarningHeader(199, 'agent', '"text"');
    }

    public function testBuild()
    {
        $header = new WarningHeader(199, 'agent', 'text');

        $expected = \sprintf('%s: %s', $header->getFieldName(), $header->getFieldValue());

        $this->assertSame($expected, $header->__toString());
    }

    public function testIterator()
    {
        $header = new WarningHeader(199, 'agent', 'text');

        $this->assertSame(
            [
                $header->getFieldName(),
                $header->getFieldValue(),
            ],
            \iterator_to_array($header->getIterator())
        );
    }
}
