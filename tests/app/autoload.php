<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Dotenv\Dotenv;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$dotenv = new Dotenv();
$dotenv->load(getcwd() . '/.env');

return $loader;
