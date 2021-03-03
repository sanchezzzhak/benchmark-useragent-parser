<?php


namespace App\Command;

use App\Helpers\SizeHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;

class Compare extends Command
{
    protected static $defaultName = 'report:compare';

    protected function configure()
    {
        $this->addArgument(
            'report',
            InputArgument::REQUIRED,
            'Set reportId'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SizeHelper $helperSize */
        $helperSize = $this->getHelper('sizeBytes');

        $basePath = realpath(__DIR__ . '/../../');
        $reportFolderName = $input->getArgument('report');
        $reportFolder = sprintf('data/%s', $reportFolderName);
        $output->writeln("report folder: {$reportFolder}");

        if (!is_dir($reportFolder)) {
            $output->writeln("<error>>report folder `{$reportFolder}` not found</error>");
            return;
        }
        // get all log fixture
        $fixtureGlobPatter = $basePath . DIRECTORY_SEPARATOR . $reportFolder . DIRECTORY_SEPARATOR . 'fixture-*.log';
        $fixtureLogs = glob($fixtureGlobPatter, GLOB_BRACE);

        var_dump($fixtureLogs);

        // read multi files
        $handles = [];
        foreach ($fixtureLogs as $key => $fileLog) {
            preg_match('~fixture-(.+)\.log$~i', $fileLog, $math);
            $parserId = $math[1];
            $handles[$parserId] = fopen($fixtureLogs[$key], 'r');
        }
        $iterate = 0;
        while ($handles !== []) {

            $reportData = [];
            foreach ($handles as $handleParserId => $handle) {
                if (!feof($handles[$handleParserId])) {
                    fclose($handles[$handleParserId]);
                    unset($handles[$handleParserId]);
                    continue;
                }
                $line = fgets($handles[$handleParserId]);

                if (empty($line)) {
                    continue;
                }
                $json = json_decode($line, true);
                $reportData['user_agent'] = $json['user_agent'];
                $reportData[$handleParserId]['result'] = $json['result'] ?? [];
                $reportData[$handleParserId]['memory'] = $helperSize->formatBytes($json['memory']);
                $reportData[$handleParserId]['time'] = $json['time'];
            }

            // write common file

            $iterate++;
        }


        // record detail format
        /*
        -
         useragent:
         parsers:
            'parserId': results
         */
        // overage parser

    }

}