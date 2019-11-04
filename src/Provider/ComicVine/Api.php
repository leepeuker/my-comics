<?php declare(strict_types=1);

namespace App\Provider\ComicVine;

use App\Provider\ComicVine\Resource\Issue;
use App\Provider\ComicVine\Resource\Volume;
use App\ValueObject\Id;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class Api
{
    private string $apiKey;

    private ClientInterface $client;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function fetchIssue(Id $id) : Issue\Dto
    {
        $request = new Request('GET', 'https://comicvine.gamespot.com/api/issue/4000-' . $id . '/?format=json&field_list=id,image,name,description,volume,store_date,issue_number&api_key=' . $this->apiKey);

        return Issue\Dto::createFromArray($this->sendRequest($request));
    }

    public function fetchVolume(Id $id) : Volume\Dto
    {
        $request = new Request('GET', 'https://comicvine.gamespot.com/api/volume/4050-' . $id . '/?format=json&field_list=id,name,description,publisher&api_key=' . $this->apiKey);

        return Volume\Dto::createFromArray($this->sendRequest($request));
    }

    private function sendRequest(RequestInterface $request) : array
    {
        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Comic Vine api error. Response status code: ' . $response->getStatusCode());
        }

        $body = json_decode((string)$response->getBody(), true);

        if (is_array($body['results']) === false) {
            throw new \RuntimeException('Comic Vine api error. Results in wrong format: ' . gettype($body['results']));
        }

        return $body['results'];
    }
}