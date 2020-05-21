<?php declare(strict_types=1);

namespace App\Command\Image;

use App\Component\Image\Service;
use App\Util\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupDatabase extends Command
{
    protected static $defaultName = 'image:cleanup:database';

    private File $fileUtil;

    private Service $imageService;

    public function __construct(Service $imageService, File $fileUtil)
    {
        parent::__construct();
        $this->imageService = $imageService;
        $this->fileUtil = $fileUtil;
    }

    protected function configure() : void
    {
        $this->setDescription('Delete images in the database which files do not exist.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //TODO

        return 0;
    }
}