<?php


namespace Console;


use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upload\FileUploader;

class UploadCommand extends ContainerAwareCommand
{
    /** @var  LoggerInterface */
    protected $logger;
    /** @var  FileUploader */
    protected $uploader;

    protected function configure()
    {
        $this->setName('worker:upload')
            ->addArgument('count', InputArgument::OPTIONAL, 'number of files to process')
            ->setDescription('Upload next images to remote storage');
    }


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->uploader = $this->getContainer()->get('file.uploader');
        $this->logger = $this->getContainer()->get('logger');

        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count');

        $list = $this->getFileList(__DIR__.'/../../images_resized');
        $processed = 0;
        foreach ($list as $file) {
            if ($count && $processed == $count) {
                break;
            }

            $filename = basename($file);
            $this->uploader->upload($file);

            $this->logger->info("Process $file");
            $output->writeln("Process $file");

            $processed++;
        }

        $output->writeln('Done');
    }

    /**
     * @param $path
     * @return array
     */
    protected function getFileList($path)
    {
        $fullList = [];
            $list = glob($path.'/*');
            $fullList = array_merge($fullList, $list);

        return $fullList;
    }
}