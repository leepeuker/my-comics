<?php declare(strict_types=1);

namespace App\Command;

use App\Component\Image\Repository;
use App\Util\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Image extends Command
{
    protected static $defaultName = 'image:cleanup';

    private File $fileUtil;

    private Repository $imageRepository;

    public function __construct(Repository $imageRepository, File $fileUtil)
    {
        parent::__construct();
        $this->imageRepository = $imageRepository;
        $this->fileUtil = $fileUtil;
    }

    protected function configure() : void
    {
        $this->setDescription('Delete images which do not exist in the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imageNames = $this->imageRepository->fetchAllNames();
        $imagesPath = __DIR__ . '/../../public/images';

        foreach ($this->getExistingFileNames($imagesPath) as $fileName) {
            if (in_array('images/' . (string)$fileName, $imageNames, true) === false) {
                $this->fileUtil->delete($imagesPath . '/' . (string)$fileName);
            }
        }

        return 0;
    }

    protected function getExistingFileNames(string $imagesPath) : array
    {
        $fileNamesFiltered = scandir($imagesPath);
        if ($fileNamesFiltered === false) {
            throw new \RuntimeException('Could not get files in directory: ' . $imagesPath);
        }
        $fileNamesFiltered = array_diff($fileNamesFiltered, ['.', '..', '.gitkeep']);

        return $fileNamesFiltered;
    }
}