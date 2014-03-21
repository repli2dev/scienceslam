<?php
include_once __DIR__ . "/bootstrap.php";

// Check if migrations should be done
$dm = new DatabaseMigrator($container->getByType('Nette\\Database\\Context'), __DIR__ . '/../resources/migrations/');
$dm->migrate();