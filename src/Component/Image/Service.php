<?php declare(strict_types=1);

namespace App\Component\Image;

use App\Util;
use App\ValueObject\Id;
use App\ValueObject\Url;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Service
{
    private const COVER_IMAGE_PATH = __DIR__ . '/../../../public/images/covers/';
    private const VALID_MIME_TYPES = ['image/jpeg', 'image/png'];

    private ClientInterface $client;

    private Util\File $fileUtil;

    private Repository $repository;

    public function __construct(Repository $repository, ClientInterface $client, Util\File $fileUtil)
    {
        $this->repository = $repository;
        $this->client = $client;
        $this->fileUtil = $fileUtil;
    }

    public function createFromUploadedFile(UploadedFile $coverImage) : Entity
    {
        $mimeType = $coverImage->getMimeType();
        if (in_array($mimeType, self::VALID_MIME_TYPES, true) === false) {
            if ($mimeType !== null) {
                throw new \RuntimeException('Cover image has an invalid mime type: ' . $mimeType);
            }
            throw new \RuntimeException('Cover image has no mime type.');
        }

        $fileName = $coverImage->getClientOriginalName();
        if ($fileName === null) {
            throw new \RuntimeException('Cover image provides no file name.');
        }

        $coverImage->move(self::COVER_IMAGE_PATH, $fileName);

        return $this->repository->create($fileName);
    }

    public function createFromUrl(Url $fileUrl) : Entity
    {
        $fileName = $this->createFileNameFromUrl($fileUrl);

        $this->fileUtil->write(self::COVER_IMAGE_PATH . $fileName, $this->fetchImage($fileUrl)->getContents());

        return $this->repository->create($fileName);
    }

    public function fetchByFileName(Url $fileUrl) : ?Entity
    {
        return $this->repository->fetchByFileName($this->createFileNameFromUrl($fileUrl));
    }

    public function fetchById(Id $id) : ?Entity
    {
        return $this->repository->fetchById($id);
    }

    private function createFileNameFromUrl(Url $fileUrl) : string
    {
        $urlFileName = explode('/', (string)$fileUrl->getPath())[4];
        if (empty($urlFileName) === true) {
            throw new RuntimeException('No filename found in url.');
        }

        return $urlFileName;
    }

    private function fetchImage(Url $fileUrl) : StreamInterface
    {
        $response = $this->client->sendRequest(new Request('GET', (string)$fileUrl));
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Invalid status code: ' . $response->getStatusCode());
        }

        return $response->getBody();
    }
}