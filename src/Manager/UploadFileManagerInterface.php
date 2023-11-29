<?php

namespace App\Manager;

use App\Exception\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface UploadFileManagerInterface
{
    public function deleteBookFile(int $id, string $fileName): void;

    public function uploadBookFile(int $bookId, UploadedFile $file): string;
}
