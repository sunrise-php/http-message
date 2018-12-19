<?php

namespace Sunrise\Http\Message\Tests;

use Http\Psr7Test\BaseTest;
use Http\Psr7Test\MessageTrait;
use Sunrise\Http\Message\Message;

class MessageIntegrationTest extends BaseTest
{
	use MessageTrait;

	protected $skippedTests = [
		'testWithoutHeader' => true,
	];

	protected function getMessage()
	{
		return new Message();
	}
}
