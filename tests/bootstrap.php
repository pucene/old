<?php

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

$loader = require $file;

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
