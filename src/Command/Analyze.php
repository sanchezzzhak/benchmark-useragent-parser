<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 * todo rename report:parse
 * Class Analyze
 * @package App\Command
 */
class Analyze extends Command
{
    protected function configure()
    {
        $this->setName('report:analyze');
        $this->addArgument(
            'report',
            InputArgument::OPTIONAL,
            'Set report name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = dirname(__DIR__) . '/Parser';
        $reportFolderName = date('Y-m-d');
        $reportFolder = sprintf('data/%s', $reportFolderName);

        $output->writeln("reportId: {$reportFolderName}");
        $output->writeln("report folder: {$reportFolder}");

        if (!is_dir($reportFolder)) {
            if (!mkdir($reportFolder, 0777, $recursive = false) && !is_dir($reportFolder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $reportFolder));
            }
        }

        $scriptFolders = glob($basePath . '/*', GLOB_ONLYDIR);
        foreach ($scriptFolders as $folder) {
            $folderName = pathinfo($folder, PATHINFO_BASENAME);
            $output->writeln("ready to start parser {$folderName}");

            $phpScriptPath = realpath($folder . DIRECTORY_SEPARATOR . "parser.php");
            if (is_file($phpScriptPath)) {
                $command = implode(' ', [
                    'php',
                    $phpScriptPath,
                    '--fixtures="data/paths.json"',
                    sprintf('--report="%s/fixture-%s.log"', $reportFolder, $folderName)
                ]);

                $process = new Process($command);
                $process->setTimeout(3600);
                $rawCommand = $process->getCommandLine();

                $output->writeln("<info>run process: {$rawCommand}</info>");
                $process->start();
                do {
                    $process->checkTimeout();
                } while ($process->isRunning() && (sleep(1) !== false));
                if (!$process->isSuccessful()) {
                    throw new \Exception($process->getErrorOutput());
                }
            }

            if (is_file($folder . DIRECTORY_SEPARATOR . "parser.js")) {

            }
        }
    }
}