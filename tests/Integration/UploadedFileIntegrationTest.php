<?php

declare(strict_types=1);

namespace Sunrise\Http\Message\Tests\Integration;

use Http\Psr7Test\UploadedFileIntegrationTest as BaseUploadedFileIntegrationTest;
use Psr\Http\Message\UploadedFileInterface;
use Sunrise\Http\Message\Stream\TmpfileStream;
use Sunrise\Http\Message\UploadedFile;

class UploadedFileIntegrationTest extends BaseUploadedFileIntegrationTest
{

    /**
     * {@inheritdoc}
     */
    protected $skippedTests = [
        'testGetSize' => 'The test does not conform to the required behavior described in PSR-7',
    ];

    /**
     * {@inheritdoc}
     */
    public function createSubject(): UploadedFileInterface
    {
        return new UploadedFile(new TmpfileStream());
    }
}
