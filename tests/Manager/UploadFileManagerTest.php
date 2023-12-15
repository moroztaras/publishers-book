<?php

namespace App\Tests\Manager;

use App\Exception\UploadFileInvalidTypeException;
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
        // Set the behavior and return result for method - guessExtension
        $this->file->expects($this->once())
            ->method('guessExtension')
            ->willReturn('jpg');

        // Set the behavior and return result for method - move
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

    // Check on exception
    public function testUploadBookFileInvalidExtension(): void
    {
        $this->expectException(UploadFileInvalidTypeException::class);

        // Set the behavior and return result for method - guessExtension
        $this->file->expects($this->once())
            ->method('guessExtension')
            ->willReturn(null);

        // Run method
        $this->createManager()->uploadBookFile(1, $this->file);
    }

    public function testDeleteBookFile(): void
    {
        // Set the behavior and return result for method - remove
        $this->fs->expects($this->once())
            ->method('remove')
            ->with('/tmp/book/1/test.jpg');

        // Run method deleteBookFile
        $this->createManager()->deleteBookFile(1, 'test.jpg');
    }

    private function createManager(): UploadFileManager
    {
        return new UploadFileManager($this->fs, self::UPLOAD_DIR);
    }
}
