<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class Analyze
 * @package App\Command
 */
class Analyze extends Command
{
    /*** @var string */
    protected static $defaultName = 'analyze:start';

    protected function configure()
    {
        $this->addArgument(
            'use',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            sprintf('Select parses (%s)', implode(', ', ['*' /*...self::PARSERS*/])),
        );
        $this->addArgument(
            'folder',
            InputArgument::REQUIRED,
            'Set folder save reports'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uses = $input->getArgument('use');

    }
}