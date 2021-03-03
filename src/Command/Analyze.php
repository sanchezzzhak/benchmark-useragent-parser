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
 * Class Analyze
 * @package App\Command
 */
class Analyze extends Command
{

    protected static string $defaultName = 'analyze:start';

    protected function configure()
    {
        $this->addArgument(
            'report',
            InputArgument::OPTIONAL,
            'Set report name'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = realpath(__DIR__ . '/../Parser');
        $reportFolder = date('Y-m-d');
        $scriptFolders = glob($basePath . '/*', GLOB_ONLYDIR);
        foreach ($scriptFolders as $folder) {
            $folderName = pathinfo($folder, PATHINFO_BASENAME);
            $output->writeln("ready to start parser {$folderName}");
//            continue;

            $phpScriptPath = realpath($folder . DIRECTORY_SEPARATOR . "parser.php");
            if (is_file($phpScriptPath)) {

                $command = implode(' ', [
                    'php',
                    $phpScriptPath,
                    '--fixtures="data/paths.json"',
                    sprintf('--report="data/%s/fixture-%s.log"', $reportFolder, $folderName)
                ]);
                $process = new Process($command);
                var_dump($process->getCommandLine());

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