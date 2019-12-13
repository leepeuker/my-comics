<?php declare(strict_types=1);

namespace App\Component\Image;

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

    public function createFromUrl(string $fileUrl) : Entity
    {
        //TODO Refactor
        $response = $this->client->sendRequest(new \GuzzleHttp\Psr7\Request('GET', $fileUrl));
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Invalid status code: ' . $response->getStatusCode());
        }

        $urlFileName = explode('/', parse_url($fileUrl)['path'])[4];
        if (empty($urlFileName) === true) {
            throw new \RuntimeException('No filename found in url.');
        }

        $fileName = 'images/' . $urlFileName;

        file_put_contents(__DIR__ . '/../../../public/' . $fileName, $response->getBody()->getContents());

        return $this->repository->create($fileName);
    }

    public function fetchByFileName(string $fileName) : ?Entity
    {
        return $this->repository->fetchByFileName($fileName);
    }
}