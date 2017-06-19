<?php

namespace System;

use \Symfony\Component\DependencyInjection\ContainerAwareInterface;
use \Symfony\Component\DependencyInjection\ContainerInterface;

class Application extends \Symfony\Component\Console\Application implements ContainerAwareInterface
{
    const LOG_PATH = __DIR__.'/../../logs/system.log';
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        foreach ($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($this->container);
            }
        }
    }
}
