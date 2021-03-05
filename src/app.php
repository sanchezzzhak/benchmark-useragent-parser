#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

use App\Command\Analyze;
use App\Command\Archive;
use App\Command\Compare;
use App\Helpers\ParserHelper;
use Symfony\Component\Console\Application;

$application = new Application();

$parserHelper = new ParserHelper();
$application->getHelperSet()->set($parserHelper);

$application->add(new Analyze);
$application->add(new Compare);
$application->add(new Archive);

$application->run();



