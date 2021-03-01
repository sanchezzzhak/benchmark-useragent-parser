<?php
require __DIR__.'/../vendor/autoload.php';

use Consolidation\AnnotatedCommand\CommandFileDiscovery;
use Robo\Runner;
use Symfony\Component\Console\Output\ConsoleOutput;


$appName = "benchmark-useragent-parser";
$appVersion = '123';

$discovery = new CommandFileDiscovery();
$discovery->setSearchPattern('*.php');
$commandClasses = $discovery->discover('src/Robo', '\App\Robo');

$selfUpdateRepository = 'kak/benchmark-useragent-parser';
$configurationFilename = 'config.yml';

// Define our Runner, and pass it the command classes we provide.
$runner = new Runner($commandClasses);
$runner
    ->setSelfUpdateRepository($selfUpdateRepository)
    ->setConfigurationFilename($configurationFilename);

$output = new ConsoleOutput();
$statusCode = $runner->execute($argv, $appName, $appVersion, $output);
exit($statusCode);