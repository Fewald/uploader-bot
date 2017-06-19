<?php
require_once 'vendor/autoload.php';
date_default_timezone_set('GMT');

$container = new \System\ContainerBuilder();
return $container->getContainer();