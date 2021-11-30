<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Http\Message\Response;

/**
 * Import constants
 */
use const Sunrise\Http\Message\REASON_PHRASES;

/**
 * ResponseTest
 */
class ResponseTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $mess = new Response();

        $this->assertInstanceOf(Message::class, $mess);
        $this->assertInstanceOf(ResponseInterface::class, $mess);
    }

    /**
     * @return void
     */
    public function testStatus() : void
    {
        $mess = new Response();
        $copy = $mess->withStatus(204);

        $this->assertInstanceOf(Message::class, $copy);
        $this->assertInstanceOf(ResponseInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default values
        $this->assertSame(200, $mess->getStatusCode());
        $this->assertSame(REASON_PHRASES[200], $mess->getReasonPhrase());

        // assigned values
        $this->assertSame(204, $copy->getStatusCode());
        $this->assertSame(REASON_PHRASES[204], $copy->getReasonPhrase());
    }

    /**
     * @dataProvider figStatusProvider
     *
     * @return void
     */
    public function testFigStatus($statusCode, $reasonPhrase) : void
    {
        $mess = (new Response)->withStatus($statusCode);

        $this->assertSame($statusCode, $mess->getStatusCode());
        $this->assertSame($reasonPhrase, $mess->getReasonPhrase());
    }

    /**
     * @dataProvider invalidStatusCodeProvider
     *
     * @return void
     */
    public function testInvalidStatusCode($statusCode) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Response)->withStatus($statusCode);
    }

    /**
     * @return void
     */
    public function testUnknownStatusCode() : void
    {
        $mess = (new Response)->withStatus(599);

        $this->assertSame('Unknown Status Code', $mess->getReasonPhrase());
    }

    /**
     * @dataProvider invalidReasonPhraseProvider
     *
     * @return void
     */
    public function testInvalidReasonPhrase($reasonPhrase) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Response)->withStatus(200, $reasonPhrase);
    }

    /**
     * @return void
     */
    public function testCustomReasonPhrase() : void
    {
        $mess = (new Response)->withStatus(200, 'test');

        $this->assertSame('test', $mess->getReasonPhrase());
    }

    // Providers...

    /**
     * @return array
     */
    public function figStatusProvider() : array
    {
        return [
            [200, REASON_PHRASES[200] ?? ''],
        ];
    }

    /**
     * @return array
     */
    public function invalidStatusCodeProvider() : array
    {
        return [
            [0],
            [99],
            [600],

            // other types
            [true],
            [false],
            ['100'],
            [100.0],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }

    /**
     * @return array
     */
    public function invalidReasonPhraseProvider() : array
    {
        return [
            ["bar\0baz"],
            ["bar\nbaz"],
            ["bar\rbaz"],

            // other types
            [true],
            [false],
            [1],
            [1.1],
            [[]],
            [new \stdClass],
            [\STDOUT],
            [null],
            [function () {
            }],
        ];
    }
}
