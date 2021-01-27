<?php
$loader = require(__DIR__ . '/vendor/autoload.php');
$loader->addPsr4('Be\\Sf\\Cache\\', __DIR__ . '/cache');
$loader->addPsr4('Be\\Sf\\Data\\', __DIR__ . '/data');

$runtime = new \Be\Sf\Runtime\Driver();
$runtime->setRootPath(__DIR__);
\Be\F\Runtime\RuntimeFactory::setInstance($runtime);
$runtime->execute();
