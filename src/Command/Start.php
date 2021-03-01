<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Start
 * @package App\Command
 */
class Start extends Command
{
    protected static string $defaultName = 'app:start';

    private const MATOMO = 'MatomoDeviceDetector';          # https://github.com/matomo-org/device-detector
    private const WHICH_BROWSER = 'WhichBrowserParserPHP';  # https://github.com/WhichBrowser/Parser-PHP
    private const MIMMI20 = 'Mimmi20BrowserDetector';       # https://github.com/mimmi20/BrowserDetector

    private const PARSERS = [
        self::WHICH_BROWSER,
        self::MATOMO,
        self::MIMMI20,
    ];

    protected function configure()
    {
        $this->addArgument(
            'use',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            sprintf('Select parses (%s)', implode(', ', ['*', ...self::PARSERS])),
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uses = $input->getArgument('use');

    }
}