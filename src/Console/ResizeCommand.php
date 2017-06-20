<?php


namespace Console;

use Resizer\ImageResizer\ImageResizer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResizeCommand extends ContainerAwareCommand
{
    /** @var ImageResizer */
    protected $resizer;

    protected $extensions = ['jpg', 'png'];

    protected function configure()
    {
        $this->setName('worker:resize')
            ->addArgument('count', InputArgument::OPTIONAL, 'number of files to process')
            ->setDescription('Resizes files from resize queue');
    }


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->resizer = $this->getContainer()->get('resizer.image');

        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count');

        $list = $this->getFileList(__DIR__.'/../../images');

        foreach ($list as $file) {
            $this->resizer->resize($file);
        }
    }

    /**
     * @param $path
     * @return array
     */
    protected function getFileList($path)
    {
        $fullList = [];
        foreach ($this->extensions as $ext) {
            $list = glob($path.'/*.'.$ext);
            $fullList = array_merge($fullList, $list);
        }

        return $fullList;
    }
}
