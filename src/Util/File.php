<?php declare(strict_types=1);

namespace App\Util;

use App\Exception\CouldNotDeleteFile;

class File
{
    public function delete(string $filePath) : void
    {
        if ($this->fileExist($filePath) === false) {
            return;
        }

        if (unlink($filePath) === false) {
            throw new CouldNotDeleteFile('Could not delete file: ' . $filePath);
        }
    }

    public function fileExist(string $filePath) : bool
    {
        return file_exists($filePath);
    }

    public function write(string $filePath, string $content) : void
    {
        if (file_put_contents($filePath, $content) === false) {
            throw new \RuntimeException('Could not write to file: ' . $filePath);
        }
    }
}