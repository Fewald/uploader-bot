<?php


namespace Console;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResizeCommand extends ContainerAwareCommand
{
    /** @var LoggerInterface */
    protected $logger;

    protected function configure()
    {
        $this->setName('worker:resize')
            ->addArgument('count', InputArgument::OPTIONAL, 'number of files to process')
            ->setDescription('Resizes files from resize queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count');

        $list = $output->writeln(scandir(__DIR__.'/../../images/'));
    }
}
