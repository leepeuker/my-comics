<?php declare(strict_types=1);

namespace App\Util;

class File
{
    public function delete(string $filePath) : void
    {
        if (unlink($filePath) === false) {
            throw new \RuntimeException('Could not delete file: ' . $filePath);
        }
    }

    public function write(string $filePath, string $content) : void
    {
        if (file_put_contents($filePath, $content) === false) {
            throw new \RuntimeException('Could not write to file: ' . $filePath);
        }
    }
}