<?php

namespace App\Manager;

use App\Exception\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadFileManager implements UploadFileManagerInterface
{
    private const LINK_BOOK_PATTERN = '/upload/book/%d/%s';

    public function __construct(
        private readonly Filesystem $fs,
        private readonly string $uploadDir
    ) {
    }

    public function uploadBookFile(int $bookId, UploadedFile $file): string
    {
        // Get extension
        $extension = $file->guessExtension();
        if (null === $extension) {
            throw new UploadFileInvalidTypeException();
        }

        // Set unique name for file
        $uniqueName = Uuid::v4()->toRfc4122().'.'.$extension;

        // Move file from temp dir in dir project
        $file->move($this->getUploadPathForBook($bookId), $uniqueName);

        return sprintf(self::LINK_BOOK_PATTERN, $bookId, $uniqueName);
    }

    public function deleteBookFile(int $id, string $fileName): void
    {
        $this->fs->remove($this->getUploadPathForBook($id).DIRECTORY_SEPARATOR.$fileName);
    }

    private function getUploadPathForBook(int $id): string
    {
        return $this->uploadDir.DIRECTORY_SEPARATOR.'book'.DIRECTORY_SEPARATOR.$id;
    }
}
