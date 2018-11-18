<?php

namespace Sunrise\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sunrise\Http\Message\ResponseFactory;

class ResponseFactoryTest extends TestCase
{
	public function testConstructor()
	{
		$factory = new ResponseFactory();

		$this->assertInstanceOf(ResponseFactoryInterface::class, $factory);
	}

	public function testCreateResponse()
	{
		$statusCode = 204;
		$reasonPhrase = 'No Content';

		$response = (new ResponseFactory)
		->createResponse($statusCode, $reasonPhrase);

		$this->assertInstanceOf(ResponseInterface::class, $response);
		$this->assertEquals($statusCode, $response->getStatusCode());
		$this->assertEquals($reasonPhrase, $response->getReasonPhrase());
	}
}
