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
    public function createSubject(): UploadedFileInterface
    {
        return new UploadedFile(new TmpfileStream());
    }
}
