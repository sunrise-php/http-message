<?php

namespace Sunrise\Http\Message\Tests;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Message\Message;
use Sunrise\Http\Message\Response;

use const Sunrise\Http\Message\PHRASES;

class ResponseTest extends TestCase
{
	public function testConstructor()
	{
		$mess = new Response();

		$this->assertInstanceOf(Message::class, $mess);
		$this->assertInstanceOf(ResponseInterface::class, $mess);
	}

	public function testStatus()
	{
		$mess = new Response();
		$copy = $mess->withStatus(204);

		$this->assertInstanceOf(Message::class, $copy);
		$this->assertInstanceOf(ResponseInterface::class, $copy);
		$this->assertNotEquals($mess, $copy);

		// default values
		$this->assertEquals(200, $mess->getStatusCode());
		$this->assertEquals(PHRASES[200], $mess->getReasonPhrase());

		// assigned values
		$this->assertEquals(204, $copy->getStatusCode());
		$this->assertEquals(PHRASES[204], $copy->getReasonPhrase());
	}

	/**
	 * @dataProvider figStatusProvider
	 */
	public function testFigStatus($statusCode, $reasonPhrase)
	{
		$mess = (new Response)->withStatus($statusCode);

		$this->assertEquals($statusCode, $mess->getStatusCode());
		$this->assertEquals($reasonPhrase, $mess->getReasonPhrase());
	}

	/**
	 * @dataProvider invalidStatusCodeProvider
	 */
	public function testInvalidStatusCode($statusCode)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Response)->withStatus($statusCode);
	}

	public function testUnknownStatusCode()
	{
		$mess = (new Response)->withStatus(599);

		$this->assertEquals('Unknown Status Code', $mess->getReasonPhrase());
	}

	/**
	 * @dataProvider invalidReasonPhraseProvider
	 */
	public function testInvalidReasonPhrase($reasonPhrase)
	{
		$this->expectException(\InvalidArgumentException::class);

		(new Response)->withStatus(200, $reasonPhrase);
	}

	public function testCustomReasonPhrase()
	{
		$mess = (new Response)->withStatus(200, 'test');

		$this->assertEquals('test', $mess->getReasonPhrase());
	}

	// Providers...

	public function figStatusProvider()
	{
		return
		[
    		[
    			StatusCodeInterface::STATUS_CONTINUE,
    			PHRASES[StatusCodeInterface::STATUS_CONTINUE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_SWITCHING_PROTOCOLS,
    			PHRASES[StatusCodeInterface::STATUS_SWITCHING_PROTOCOLS] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PROCESSING,
    			PHRASES[StatusCodeInterface::STATUS_PROCESSING] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_OK,
    			PHRASES[StatusCodeInterface::STATUS_OK] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_CREATED,
    			PHRASES[StatusCodeInterface::STATUS_CREATED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_ACCEPTED,
    			PHRASES[StatusCodeInterface::STATUS_ACCEPTED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NON_AUTHORITATIVE_INFORMATION,
    			PHRASES[StatusCodeInterface::STATUS_NON_AUTHORITATIVE_INFORMATION] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NO_CONTENT,
    			PHRASES[StatusCodeInterface::STATUS_NO_CONTENT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_RESET_CONTENT,
    			PHRASES[StatusCodeInterface::STATUS_RESET_CONTENT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PARTIAL_CONTENT,
    			PHRASES[StatusCodeInterface::STATUS_PARTIAL_CONTENT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_MULTI_STATUS,
    			PHRASES[StatusCodeInterface::STATUS_MULTI_STATUS] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_ALREADY_REPORTED,
    			PHRASES[StatusCodeInterface::STATUS_ALREADY_REPORTED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_IM_USED,
    			PHRASES[StatusCodeInterface::STATUS_IM_USED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_MULTIPLE_CHOICES,
    			PHRASES[StatusCodeInterface::STATUS_MULTIPLE_CHOICES] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
    			PHRASES[StatusCodeInterface::STATUS_MOVED_PERMANENTLY] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_FOUND,
    			PHRASES[StatusCodeInterface::STATUS_FOUND] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_SEE_OTHER,
    			PHRASES[StatusCodeInterface::STATUS_SEE_OTHER] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NOT_MODIFIED,
    			PHRASES[StatusCodeInterface::STATUS_NOT_MODIFIED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_USE_PROXY,
    			PHRASES[StatusCodeInterface::STATUS_USE_PROXY] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_RESERVED,
    			PHRASES[StatusCodeInterface::STATUS_RESERVED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_TEMPORARY_REDIRECT,
    			PHRASES[StatusCodeInterface::STATUS_TEMPORARY_REDIRECT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PERMANENT_REDIRECT,
    			PHRASES[StatusCodeInterface::STATUS_PERMANENT_REDIRECT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_BAD_REQUEST,
    			PHRASES[StatusCodeInterface::STATUS_BAD_REQUEST] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_UNAUTHORIZED,
    			PHRASES[StatusCodeInterface::STATUS_UNAUTHORIZED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PAYMENT_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_PAYMENT_REQUIRED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_FORBIDDEN,
    			PHRASES[StatusCodeInterface::STATUS_FORBIDDEN] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NOT_FOUND,
    			PHRASES[StatusCodeInterface::STATUS_NOT_FOUND] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED,
    			PHRASES[StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NOT_ACCEPTABLE,
    			PHRASES[StatusCodeInterface::STATUS_NOT_ACCEPTABLE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PROXY_AUTHENTICATION_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_PROXY_AUTHENTICATION_REQUIRED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_REQUEST_TIMEOUT,
    			PHRASES[StatusCodeInterface::STATUS_REQUEST_TIMEOUT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_CONFLICT,
    			PHRASES[StatusCodeInterface::STATUS_CONFLICT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_GONE,
    			PHRASES[StatusCodeInterface::STATUS_GONE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_LENGTH_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_LENGTH_REQUIRED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PRECONDITION_FAILED,
    			PHRASES[StatusCodeInterface::STATUS_PRECONDITION_FAILED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PAYLOAD_TOO_LARGE,
    			PHRASES[StatusCodeInterface::STATUS_PAYLOAD_TOO_LARGE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_URI_TOO_LONG,
    			PHRASES[StatusCodeInterface::STATUS_URI_TOO_LONG] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_UNSUPPORTED_MEDIA_TYPE,
    			PHRASES[StatusCodeInterface::STATUS_UNSUPPORTED_MEDIA_TYPE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_RANGE_NOT_SATISFIABLE,
    			PHRASES[StatusCodeInterface::STATUS_RANGE_NOT_SATISFIABLE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_EXPECTATION_FAILED,
    			PHRASES[StatusCodeInterface::STATUS_EXPECTATION_FAILED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_IM_A_TEAPOT,
    			PHRASES[StatusCodeInterface::STATUS_IM_A_TEAPOT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_MISDIRECTED_REQUEST,
    			PHRASES[StatusCodeInterface::STATUS_MISDIRECTED_REQUEST] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
    			PHRASES[StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_LOCKED,
    			PHRASES[StatusCodeInterface::STATUS_LOCKED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_FAILED_DEPENDENCY,
    			PHRASES[StatusCodeInterface::STATUS_FAILED_DEPENDENCY] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_UPGRADE_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_UPGRADE_REQUIRED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_PRECONDITION_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_PRECONDITION_REQUIRED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_TOO_MANY_REQUESTS,
    			PHRASES[StatusCodeInterface::STATUS_TOO_MANY_REQUESTS] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE,
    			PHRASES[StatusCodeInterface::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS,
    			PHRASES[StatusCodeInterface::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
    			PHRASES[StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NOT_IMPLEMENTED,
    			PHRASES[StatusCodeInterface::STATUS_NOT_IMPLEMENTED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_BAD_GATEWAY,
    			PHRASES[StatusCodeInterface::STATUS_BAD_GATEWAY] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE,
    			PHRASES[StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_GATEWAY_TIMEOUT,
    			PHRASES[StatusCodeInterface::STATUS_GATEWAY_TIMEOUT] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_VERSION_NOT_SUPPORTED,
    			PHRASES[StatusCodeInterface::STATUS_VERSION_NOT_SUPPORTED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_VARIANT_ALSO_NEGOTIATES,
    			PHRASES[StatusCodeInterface::STATUS_VARIANT_ALSO_NEGOTIATES] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_INSUFFICIENT_STORAGE,
    			PHRASES[StatusCodeInterface::STATUS_INSUFFICIENT_STORAGE] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_LOOP_DETECTED,
    			PHRASES[StatusCodeInterface::STATUS_LOOP_DETECTED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NOT_EXTENDED,
    			PHRASES[StatusCodeInterface::STATUS_NOT_EXTENDED] ?? '',
    		],
    		[
    			StatusCodeInterface::STATUS_NETWORK_AUTHENTICATION_REQUIRED,
    			PHRASES[StatusCodeInterface::STATUS_NETWORK_AUTHENTICATION_REQUIRED] ?? '',
    		],
		];
	}

	public function invalidStatusCodeProvider()
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
			[function(){}],
		];
	}

	public function invalidReasonPhraseProvider()
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
			[function(){}],
		];
	}
}
