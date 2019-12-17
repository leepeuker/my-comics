<?php declare(strict_types=1);

namespace App\Component\Image;

use App\Util;
use App\ValueObject\Url;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;

class Service
{
    private ClientInterface $client;

    private Util\File $fileUtil;

    private Repository $repository;

    public function __construct(Repository $repository, ClientInterface $client, Util\File $fileUtil)
    {
        $this->repository = $repository;
        $this->client = $client;
        $this->fileUtil = $fileUtil;
    }

    public function createFromUrl(Url $fileUrl) : Entity
    {
        $fileName = $this->createFileNameFromUrl($fileUrl);

        $this->fileUtil->write(__DIR__ . '/../../../public/' . $fileName, $this->fetchImage($fileUrl)->getContents());

        return $this->repository->create($fileName);
    }

    public function fetchByFileName(Url $fileUrl) : ?Entity
    {
        return $this->repository->fetchByFileName($this->createFileNameFromUrl($fileUrl));
    }

    private function createFileNameFromUrl(Url $fileUrl) : string
    {
        $urlFileName = explode('/', (string)$fileUrl->getPath())[4];
        if (empty($urlFileName) === true) {
            throw new \RuntimeException('No filename found in url.');
        }

        return 'images/' . $urlFileName;
    }

    private function fetchImage(Url $fileUrl) : StreamInterface
    {
        $response = $this->client->sendRequest(new Request('GET', (string)$fileUrl));
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Invalid status code: ' . $response->getStatusCode());
        }

        return $response->getBody();
    }
}