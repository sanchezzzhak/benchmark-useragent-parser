<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    protected function configure()
    {
        $this->setName('report:start');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();

        // todo add run commands step-to-step

        // report:analyze  -- analyze all files for report folder
        // report:compare  -- merge and compare file
        // report:archive  -- pack result to zip and move archive site/report
        // report:cleaner  -- remove files report

    }
}