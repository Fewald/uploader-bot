<?php
namespace System;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Queue\ResizeQueue;
use Queue\UploadQueue;
use Resizer\ImageResizer\GDImageResizer;
use Symfony\Component\DependencyInjection\Reference;
use Upload\FileUploader;
use Upload\GoogleDriveApi;

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

        $this->container->register('gearman.client', \GearmanClient::class)
            ->addMethodCall('addServer');
        /**
         * Resize
         */
        $this->container->register('queue.resize', ResizeQueue::class)
            ->addArgument(new Reference('gearman.client'));

        $this->container->register('resizer.image', GDImageResizer::class)
            ->addArgument(__DIR__.'/../../images')
            ->addArgument(__DIR__.'/../../images_resized');

        /**
         * Upload
         */
        $this->container->register('queue.upload', UploadQueue::class)
            ->addArgument(new Reference('gearman.client'));

        $this->container->register('file.uploader', FileUploader::class)
            ->addArgument(new Reference('api.google-drive'));
        $this->container->register('api.google-drive', GoogleDriveApi::class);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }
}