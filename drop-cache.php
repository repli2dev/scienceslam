<?php

use Nette\IOException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;

require __DIR__ . '/libs/autoload.php';

$localConfig = __DIR__ . '/app/config/config.local.neon';

if (!file_exists($localConfig)) {
    die('No local config file. Cannot check access.');
}

$config = Neon::decode(file_get_contents($localConfig));
if (!isset($config['parameters']['maintenance']['key'])) {
    die('No maintenance.key in local config file. Cannot check access');
}
if (isset($_GET['key']) && $config['parameters']['maintenance']['key'] && $config['parameters']['maintenance']['key'] === $_GET['key']) {
    try {
        FileSystem::delete(__DIR__ . '/temp/cache/');
        FileSystem::createDir(__DIR__ . '/temp/cache/');
    } catch (IOException $exception) {
        die('Dropping cache has failed.');
    }
    die('Dropping cache was finished.');
}
die('Unauthorized.');
