<?php



namespace Console;

use Psr\Log\LoggerInterface;
use Queue\Message;
use Queue\ResizeQueue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleCommand extends ContainerAwareCommand
{
    /** @var  LoggerInterface */
    protected $logger;

    protected $extensions = ['jpg', 'png'];

    protected function configure()
    {
        $this->setName('schedule')
            ->addArgument('count', InputArgument::OPTIONAL, 'number of files to process')
            ->setDescription('Resize next images from the queue');
    }


    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('logger');
        $this->resizer = $this->getContainer()->get('resizer.image');

        parent::initialize($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getArgument('count');
        /** @var ResizeQueue $queue */
        $queue = $this->getContainer()->get('queue.resize');

        $list = $this->getFileList(__DIR__.'/../../images');
        $processed = 0;
        foreach ($list as $file) {
            if ($count && $processed == $count) {
                break;
            }

            $message = new Message($file);
            $queue->push($message);


            $this->logger->info("Process $file");
            $output->writeln("Process $file");

            $processed++;
        }

        $output->writeln('Done');
    }

    /**
     * @param $path string
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