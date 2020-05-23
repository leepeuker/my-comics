<?php declare(strict_types=1);

namespace App\Component\Image;

use App\Exception\CouldNotDeleteFile;
use App\Util;
use App\ValueObject\Id;
use App\ValueObject\Url;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Service
{
    private const COVER_IMAGE_PATH = __DIR__ . '/../../../public/images/covers/';
    private const VALID_MIME_TYPES = ['image/jpeg', 'image/png'];

    private ClientInterface $client;

    private Util\File $fileUtil;

    private LoggerInterface $logger;

    private Repository $repository;

    public function __construct(Repository $repository, ClientInterface $client, Util\File $fileUtil, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->client = $client;
        $this->fileUtil = $fileUtil;
        $this->logger = $logger;
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
        $fileName = $this->getFileNameFromUrl($fileUrl);

        $this->fileUtil->write(self::COVER_IMAGE_PATH . $fileName, $this->fetchImage($fileUrl)->getContents());

        return $this->repository->create($fileName);
    }

    public function deleteById(Id $id) : void
    {
        $image = $this->findById($id);

        $this->repository->deleteById($id);

        if ($image !== null) {
            try {
                $this->fileUtil->delete(self::COVER_IMAGE_PATH . $image->getFileName());
            } catch (CouldNotDeleteFile $e) {
                $this->logger->warning($e->getMessage());
            }
        }
    }

    public function fetchAllNames() : array
    {
        return $this->repository->fetchAllNames();
    }

    public function fetchByFileName(Url $fileUrl) : ?Entity
    {
        return $this->repository->fetchByFileName($this->getFileNameFromUrl($fileUrl));
    }

    public function fetchById(Id $id) : Entity
    {
        return $this->repository->fetchById($id);
    }

    public function findById(Id $id) : ?Entity
    {
        return $this->repository->findById($id);
    }

    public function findImageIdByComicId(Id $comicId) : ?Id
    {
        $image = $this->repository->findByComicId($comicId);

        return $image === null ? null : $image->getId();
    }

    private function fetchImage(Url $fileUrl) : StreamInterface
    {
        $response = $this->client->sendRequest(new Request('GET', (string)$fileUrl));
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Invalid status code: ' . $response->getStatusCode());
        }

        return $response->getBody();
    }

    private function getFileNameFromUrl(Url $fileUrl) : string
    {
        $urlFileName = basename($fileUrl->getPath());
        if ($urlFileName === '') {
            throw new RuntimeException('No filename found in url: ' . $fileUrl);
        }

        return $urlFileName;
    }
}