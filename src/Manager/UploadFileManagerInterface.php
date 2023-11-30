<?php

namespace App\Manager;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadFileManagerInterface
{
    public function deleteBookFile(int $id, string $fileName): void;

    public function uploadBookFile(int $bookId, UploadedFile $file): string;
}
