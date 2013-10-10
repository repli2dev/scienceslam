<?php
include_once __DIR__ . "/bootstrap.php";

// Check if migrations should be done
$dm = new DatabaseMigrator($container->getService('nette.database.default'), __DIR__ . '/../resources/migrations/');
$dm->migrate();