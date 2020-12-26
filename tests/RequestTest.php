<?php declare(strict_types=1);

namespace Sunrise\Http\Message\Tests;

/**
 * Import classes
 */
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Http\Message\Request;
use Sunrise\Uri\UriFactory;

/**
 * RequestTest
 */
class RequestTest extends TestCase
{

    /**
     * @return void
     */
    public function testConstructor() : void
    {
        $mess = new Request();

        $this->assertInstanceOf(Message::class, $mess);
        $this->assertInstanceOf(RequestInterface::class, $mess);
    }

    /**
     * @return void
     */
    public function testMethod() : void
    {
        $mess = new Request();
        $copy = $mess->withMethod('POST');

        $this->assertInstanceOf(Message::class, $copy);
        $this->assertInstanceOf(RequestInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default value
        $this->assertEquals('GET', $mess->getMethod());
        // assigned value
        $this->assertEquals('POST', $copy->getMethod());
    }

    /**
     * @return void
     */
    public function testLowercasedMethod() : void
    {
        $mess = (new Request)->withMethod('post');

        $this->assertEquals('POST', $mess->getMethod());
    }

    /**
     * @dataProvider figMethodProvider
     *
     * @return void
     */
    public function testFigMethod($method) : void
    {
        $mess = (new Request)->withMethod($method);

        $this->assertEquals($method, $mess->getMethod());
    }

    /**
     * @dataProvider invalidMethodProvider
     *
     * @return void
     */
    public function testInvalidMethod($method) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Request)->withMethod($method);
    }

    /**
     * @return void
     */
    public function testRequestTarget() : void
    {
        $mess = new Request();
        $copy = $mess->withRequestTarget('/path?query');

        $this->assertInstanceOf(Message::class, $copy);
        $this->assertInstanceOf(RequestInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default value
        $this->assertEquals('/', $mess->getRequestTarget());
        // assigned value
        $this->assertEquals('/path?query', $copy->getRequestTarget());
    }

    /**
     * @dataProvider uriFormProvider
     *
     * @return void
     */
    public function testRequestTargetWithDifferentUriForms($requestTarget) : void
    {
        $mess = (new Request)->withRequestTarget($requestTarget);

        $this->assertEquals($requestTarget, $mess->getRequestTarget());
    }

    /**
     * @dataProvider invalidRequestTargetProvider
     *
     * @return void
     */
    public function testInvalidRequestTarget($requestTarget) : void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Request)->withRequestTarget($requestTarget);
    }

    /**
     * @return void
     */
    public function testGetRequestTargetHavingUriWithoutPath() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost');
        $mess = (new Request)->withUri($uri);

        // returns "/" as default path
        $this->assertEquals('/', $mess->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testGetRequestTargetHavingUriWithNotAbsolutePath() : void
    {
        $uri = (new UriFactory)->createUri('not/absolute/path?query');
        $mess = (new Request)->withUri($uri);

        // returns "/" as default path
        $this->assertEquals('/', $mess->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testGetRequestTargetHavingUriWithAbsolutePath() : void
    {
        $uri = (new UriFactory)->createUri('/path');
        $mess = (new Request)->withUri($uri);

        $this->assertEquals('/path', $mess->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testGetRequestTargetHavingUriWithAbsolutePathAndQuery() : void
    {
        $uri = (new UriFactory)->createUri('/path?query');
        $mess = (new Request)->withUri($uri);

        $this->assertEquals('/path?query', $mess->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testGetRequestTargetIgnoringNewUri() : void
    {
        $uri = (new UriFactory)->createUri('/new');
        $mess = (new Request)->withRequestTarget('/primary')->withUri($uri);

        $this->assertEquals('/primary', $mess->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testUri() : void
    {
        $uri = (new UriFactory)->createUri('/');
        $mess = new Request();
        $copy = $mess->withUri($uri);

        $this->assertInstanceOf(Message::class, $copy);
        $this->assertInstanceOf(RequestInterface::class, $copy);
        $this->assertNotEquals($mess, $copy);

        // default value
        $this->assertEquals(null, $mess->getUri());
        // assigned value
        $this->assertEquals($uri, $copy->getUri());
    }

    /**
     * @return void
     */
    public function testUriWithAssigningHostHeaderFromUriHost() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost');
        $mess = (new Request)->withUri($uri);

        $this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
    }

    /**
     * @return void
     */
    public function testUriWithAssigningHostHeaderFromUriHostAndPort() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost:3000');
        $mess = (new Request)->withUri($uri);

        $this->assertEquals($uri->getHost() . ':' . $uri->getPort(), $mess->getHeaderLine('host'));
    }

    /**
     * @return void
     */
    public function testUriWithReplacingHostHeaderFromUri() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost');
        $mess = (new Request)->withHeader('host', 'example.com')->withUri($uri);

        $this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
    }

    /**
     * @return void
     */
    public function testUriWithPreservingHostHeader() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost');
        $mess = (new Request)->withHeader('host', 'example.com')->withUri($uri, true);

        $this->assertEquals('example.com', $mess->getHeaderLine('host'));
    }

    /**
     * @return void
     */
    public function testUriWithPreservingHostHeaderIfItIsEmpty() : void
    {
        $uri = (new UriFactory)->createUri('http://localhost');
        $mess = (new Request)->withUri($uri, true);

        $this->assertEquals($uri->getHost(), $mess->getHeaderLine('host'));
    }

    // Providers...

    /**
     * @return array
     */
    public function figMethodProvider() : array
    {
        return [
            ['HEAD'],
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            ['PURGE'],
            ['OPTIONS'],
            ['TRACE'],
            ['CONNECT'],
        ];
    }

    /**
     * @return array
     */
    public function invalidMethodProvider() : array
    {
        return [
            [''],
            ["BAR\0BAZ"],
            ["BAR\tBAZ"],
            ["BAR\nBAZ"],
            ["BAR\rBAZ"],
            ["BAR BAZ"],
            ["BAR,BAZ"],

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

    /**
     * @return array
     */
    public function invalidRequestTargetProvider() : array
    {
        return [
            [''],
            ["/path\0/"],
            ["/path\t/"],
            ["/path\n/"],
            ["/path\r/"],
            ["/path /"],

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

    /**
     * @return array
     */
    public function uriFormProvider() : array
    {
        return [
            // https://tools.ietf.org/html/rfc7230#section-5.3.1
            ['/path?query'],

            // https://tools.ietf.org/html/rfc7230#section-5.3.2
            ['http://localhost/path?query'],

            // https://tools.ietf.org/html/rfc7230#section-5.3.3
            ['localhost:3000'],

            // https://tools.ietf.org/html/rfc7230#section-5.3.4
            ['*'],
        ];
    }
}
