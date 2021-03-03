<?php


namespace App\Command;

use App\Helpers\ParserHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * todo rename report:merge
 * Class Compare
 * @package App\Command
 */
class Compare extends Command
{
    protected function configure()
    {
        $this->setName('report:compare');
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
        /** @var ParserHelper $parserHelper */
        $parserHelper = $this->getHelper('parser');
        $reportFolderName = $input->getArgument('report');

        $basePath = realpath(__DIR__ . '/../../');

        $reportPath = 'data' . DIRECTORY_SEPARATOR . $reportFolderName;
        $absoluteReportPath = $basePath . DIRECTORY_SEPARATOR . $reportPath;
        $saveComparePath = $reportPath . DIRECTORY_SEPARATOR . 'compare-detail.log';

        $output->writeln("select report folder: {$reportPath}");
        if (!is_dir($reportPath)) {
            $output->writeln("<error>>report folder `{$reportPath}` not found</error>");
            return;
        }

        // get all log fixtures
        $fixtureGlobPath = $absoluteReportPath . DIRECTORY_SEPARATOR . 'fixture-*.log';
        $files = glob($fixtureGlobPath, GLOB_BRACE);

        // read multi files
        $handles = [];
        foreach ($files as $fileId => $filePath) {
            preg_match('~fixture-(.+)\.log$~i', $filePath, $match);
            $parserId = $match[1];
            $handles[$parserId] = fopen(realpath($files[$fileId]), 'r');
        }

        $iterate = 0;
        $fn = fopen($saveComparePath, 'w');
        while ($handles !== []) {
            $iterate++;
            $reportData = [];
            foreach ($handles as $handleParserId => $handle) {
                if (feof($handles[$handleParserId])) {
                    fclose($handles[$handleParserId]);
                    unset($handles[$handleParserId]);
                    continue;
                }

                $line = fgets($handles[$handleParserId]);
                if (empty($line)) {
                    continue;
                }
                //
                $json = json_decode($line, true);

                $reportData['id'] = $iterate;
                $reportData['user_agent'] = $json['user_agent'];
                $reportData[$handleParserId]['result'] = $json['result'] ?? [];
                $reportData[$handleParserId]['memory'] = $parserHelper->formatBytes($json['memory']);
                $reportData[$handleParserId]['time'] = $json['time'];

                $totalResult[$handleParserId]['useragents'] = $iterate;
                $totalResult[$handleParserId]['totalTime'] += $iterate;

                if (array_key_exists('bot', $json)) {
                    $totalResult[$handleParserId]['bots']++;
                }

            }

            if ($reportData === []) {
                continue;
            }

            fwrite($fn, json_encode($reportData, true) . PHP_EOL);
        }
        fclose($fn);
    }


}