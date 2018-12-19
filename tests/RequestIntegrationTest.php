<?php

namespace Sunrise\Http\Message\Tests;

use Http\Psr7Test\RequestIntegrationTest as BaseRequestIntegrationTest;
use Sunrise\Http\Message\Request;

class RequestIntegrationTest extends BaseRequestIntegrationTest
{
	protected $skippedTests = [
		'testWithoutHeader' => true,
		'testMethod' => true,
		'testUri' => true,
	];

	public function createSubject()
	{
		return new Request();
	}
}
