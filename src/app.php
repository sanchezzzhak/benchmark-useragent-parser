#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

use App\Command\Analyze;
use App\Command\Compare;
use App\Helpers\SizeHelper;
use Symfony\Component\Console\Application;

$application = new Application();

$sizeHelper = new SizeHelper();
$application->getHelperSet()->set($sizeHelper);

$application->add(new Analyze);
$application->add(new Compare);

$application->run();



