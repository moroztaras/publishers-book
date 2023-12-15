<?php

namespace App\Tests\Manager;

use App\Manager\UploadFileManager;
use App\Tests\AbstractTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadFileManagerTest extends AbstractTestCase
{
    private const UPLOAD_DIR = '/tmp';

    private Filesystem $fs;

    private UploadedFile $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = $this->createMock(Filesystem::class);
        $this->file = $this->createMock(UploadedFile::class);
    }

    public function testUploadBookFile(): void
    {
        $this->file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        $this->file->expects($this->once())
            ->method('move')
            ->with($this->equalTo('/tmp/book/1'), $this->callback(function (string $arg) {
                if (!str_ends_with($arg, '.jpg')) {
                    return false;
                }

                return Uuid::isValid(basename($arg, '.jpg'));
            }));

        $actualPath = pathinfo($this->createManager()->uploadBookFile(1, $this->file));

        $this->assertEquals('/upload/book/1', $actualPath['dirname']);
        $this->assertEquals('jpg', $actualPath['extension']);
        $this->assertTrue(Uuid::isValid($actualPath['filename']));
    }

    private function createManager(): UploadFileManager
    {
        return new UploadFileManager($this->fs, self::UPLOAD_DIR);
    }
}
