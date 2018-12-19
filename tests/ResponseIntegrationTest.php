<?php

namespace Sunrise\Http\Message\Tests;

use Http\Psr7Test\ResponseIntegrationTest as BaseResponseIntegrationTest;
use Sunrise\Http\Message\Response;

class ResponseIntegrationTest extends BaseResponseIntegrationTest
{
	protected $skippedTests = [
		'testWithoutHeader' => true,
	];

	public function createSubject()
	{
		return new Response();
	}
}
