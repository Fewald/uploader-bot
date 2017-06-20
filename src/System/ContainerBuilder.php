<?php
namespace System;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Resizer\ImageResizer\ImageResizer;
use Symfony\Component\DependencyInjection\Reference;

class ContainerBuilder
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $container;

    public function __construct()
    {
        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();

        /**
         * Logger
         */
        $this->container->register('logger.rotating-handler', RotatingFileHandler::class)
            ->addArgument(Application::LOG_PATH)
            ->addArgument(31);

        $this->container->register('logger', Logger::class)
            ->addArgument('convert')
            ->addMethodCall('pushHandler', [new Reference('logger.rotating-handler')]);

        $this->container->register('resizer.image', ImageResizer::class)
            ->addArgument(__DIR__.'/../../images')
            ->addArgument(__DIR__.'/../../images_resized');
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }
}