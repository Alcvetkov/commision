<?php

require_once __DIR__ . '/vendor/autoload.php';

use Commission\Factory;

if (empty($argv[1]))
{
    echo 'Please choose file for processing.' . PHP_EOL;
    exit();
}

$commissionFactory = new Factory();
$commissionFactory->processFile($argv[1]);