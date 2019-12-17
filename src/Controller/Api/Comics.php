<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ComicVine;
use App\Util\Json;
use App\ValueObject\Id;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Comics extends AbstractController
{
    private ComicVine $comicVine;

    private LoggerInterface $logger;

    public function __construct(ComicVine $comicVine, LoggerInterface $logger)
    {
        $this->comicVine = $comicVine;
        $this->logger = $logger;
    }

    public function addComic(Request $request) : Response
    {
        try {
            $data = Json::decode((string)$request->getContent());
            $issue = $this->comicVine->createComicByIssueId(Id::createFromString($data['comicVineId']));
        } catch (\Throwable $t) {
            $this->logger->error($t->getMessage());
            throw new \Exception('');
        }

        return $this->json($issue);
    }
}