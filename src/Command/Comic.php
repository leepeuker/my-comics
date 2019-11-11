<?php declare(strict_types=1);

namespace App\Command;

use App\Service\ComicVine;
use App\ValueObject\Id;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Comic extends Command
{
    protected static $defaultName = 'comic:add';

    private ComicVine $comicVineService;

    public function __construct(ComicVine $comicVineService)
    {
        $this->comicVineService = $comicVineService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('comicVineId', InputArgument::REQUIRED, 'The comic vine issue id.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $comicVineId = $input->getArgument('comicVineId');
        if (is_string($comicVineId) === false) {
            throw new \InvalidArgumentException('ComicVineId invalid.');
        }

        $issue = $this->comicVineService->createComicByIssueId(Id::createFromString($comicVineId));

        $output->writeln(json_encode($issue));
    }
}