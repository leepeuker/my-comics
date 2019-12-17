<?php declare(strict_types=1);

namespace App\Component\Image;

use App\ValueObject\Url;
use Psr\Http\Client\ClientInterface;

class Service
{
    private ClientInterface $client;

    private Repository $repository;

    public function __construct(Repository $repository, ClientInterface $client)
    {
        $this->repository = $repository;
        $this->client = $client;
    }

    public function createFromUrl(Url $fileUrl) : Entity
    {
        //TODO Refactor
        $response = $this->client->sendRequest(new \GuzzleHttp\Psr7\Request('GET', (string)$fileUrl));
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Invalid status code: ' . $response->getStatusCode());
        }

        $fileName = $this->createFileNameFromUrl($fileUrl);

        file_put_contents(__DIR__ . '/../../../public/' . $fileName, $response->getBody()->getContents());

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
}