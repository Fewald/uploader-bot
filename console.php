<?php
set_time_limit(0);
chdir(__DIR__);

/** @var \Symfony\Component\DependencyInjection\Container $app */
$container = require_once __DIR__.DIRECTORY_SEPARATOR.'bootstrap.php';

/** @var \Symfony\Component\Console\Application $console */
$console = new \System\Application();
$console->add(new \Console\ResizeCommand());

$console->setContainer($container);
$console->run();