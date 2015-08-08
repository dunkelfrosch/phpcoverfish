#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/../vendor/autoload.php';

use DF\PHPCoverFish\CoverFishScanCommand;
use Symfony\Component\Console\Application;

$coverFishScan = new CoverFishScanCommand();
$application = new Application('PHPCoverFish', $coverFishScan->getVersion());
$application->add($coverFishScan);
$application->run();