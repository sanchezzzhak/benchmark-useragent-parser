<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Total extends Command
{
    protected function configure()
    {
        $this->setName('report:total');
        $this->addArgument(
            'report',
            InputArgument::REQUIRED,
            'Set reportId'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reportFolderName = $input->getArgument('report');
        $basePath = realpath(__DIR__ . '/../../');
        $reportPath = 'data' . DIRECTORY_SEPARATOR . $reportFolderName;
        $compareDetailPath = $reportPath . DIRECTORY_SEPARATOR . 'compare-detail.log';

        $fr = fopen($compareDetailPath, 'r');
        while (!feof($fr)) {
            $line = fgets($fr);
            if (false === $line) {
                break;
            }
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
        }
        fclose($fr);
    }

}