#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/../vendor/autoload.php';

use App\Command\Start as StartCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new StartCommand);


$application->run();



