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

    private array $total = [];
    private int $iterate = 0;

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

        // merge all fixtures into compare file
        $files = glob($absoluteReportPath . DIRECTORY_SEPARATOR . 'fixture-*.log', GLOB_BRACE);
        $this->mergeAndCompare($files, $saveComparePath, 'w');
        // merge all files into one compare file
        $files = glob($absoluteReportPath . DIRECTORY_SEPARATOR . 'file-*.log', GLOB_BRACE);
        $this->mergeAndCompare($files, $saveComparePath, 'w+');
        // save scores in file

    }

    private function mergeAndCompare(array $files, $saveComparePath, $mode = 'w'): void
    {
        /** @var ParserHelper $parserHelper */
        $parserHelper = $this->getHelper('parser');

        $handles = [];
        foreach ($files as $fileId => $filePath) {
            preg_match('~(?:fixture|file)-(.+)\.log$~i', $filePath, $match);
            $parserId = $match[1];
            $handles[$parserId] = fopen(realpath($files[$fileId]), 'r');
        }
        if ($handles === []) {
            return;
        }
        $fn = fopen($saveComparePath, $mode);
        while ($handles !== []) {

            $this->iterate++;

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
                $json = json_decode($line, true);
                if (empty($json)) {
                    continue;
                }

                $reportData['id'] = $this->iterate;
                $reportData['user_agent'] = $json['user_agent'];
                $reportData[$handleParserId]['result'] = $json['result'] ?? [];
                $reportData[$handleParserId]['memory'] = $parserHelper->formatBytes($json['memory']);
                $reportData[$handleParserId]['time'] = $json['time'];

                $this->calculateTotalRowScoreByResult($handleParserId, $json);
            }

            if ($reportData === []) {
                continue;
            }

            fwrite($fn, json_encode($reportData, true) . PHP_EOL);
        }
        fclose($fn);
    }

    private function scoreInitDefaultByParser(string $parserId)
    {
        if (isset($this->total[$parserId])) {
            return;
        }
        $this->total[$parserId] = [];

        $this->scoreDefaultValueByType(self::SCORE_BOT, $parserId);
        $this->scoreDefaultValueByType(self::SCORE_OS, $parserId);
        $this->scoreDefaultValueByType(self::SCORE_OS_VERSION, $parserId);
        $this->scoreDefaultValueByType(self::SCORE_OS_PLATFORM, $parserId);
        $this->scoreDefaultValueByType(self::SCORE_BROWSER, $parserId);
        $this->scoreDefaultValueByType(self::SCORE_BROWSER_VERSION, $parserId);
    }

    private function scoreDefaultValueByType(string $type, string $parserId)
    {
        if (!isset($this->total[$parserId][$type])) {
            $this->total[$parserId][$type] = 0;
        }
    }

    private function scoreIncrByType(string $type, string $parserId)
    {
        $this->total[$parserId][$type]++;
    }

    private const SCORE_BOT = 'bots';
    private const SCORE_OS = 'os';
    private const SCORE_OS_VERSION = 'osVersion';
    private const SCORE_OS_PLATFORM = 'osPlatform';

    private const SCORE_BROWSER = 'browser';
    private const SCORE_BROWSER_VERSION = 'browserVersion';


    /**
     *         // {"result":{"os":{
     * //"name":"Android",
     * //"version":"5.1",
     * //"platform":""
     * //},"client":{
     * //"type":"browser",
     * //"name":"Samsung Browser",
     * //"version":"8.2",
     * //"engine":"WebKit",
     * //"engine_version":"537.36"},"device":{"type":"smartphone","brand":"Samsung","model":"Galaxy S6"},"os_family":"Android",
     * //"browser_family":"Chrome"},"memory":104,"time":0.0023}
     */
    /**
     * @param $parserId
     * @param array $json
     */
    private function calculateRowScoreByParserMatomoDeviceDetector($parserId, array $json)
    {
        $botName = $result['bot']['name'] ?? null;
        if ((string)$botName !== '') {
            $this->scoreIncrByType(self::SCORE_BOT, $parserId);
            return;
        }
        // os
        if (isset($result['os']) && is_array($result['os'])) {
            $osName = $result['os']['name'] ?? null;
            if((string)$osName !== '') {
                $this->scoreIncrByType(self::SCORE_OS, $parserId);
            }
            $osVersion = $result['os']['version'] ?? null;
            if((string)$osVersion !== '') {
                $this->scoreIncrByType(self::SCORE_OS_VERSION, $parserId);
            }
            $osPlatform = $result['os']['platform'] ?? null;
            if((string)$osPlatform !== '') {
                $this->scoreIncrByType(self::SCORE_OS_PLATFORM, $parserId);
            }
        }
        // client:  browser, mobile app etc
        if (isset($result['client']) && is_array($result['client'])) {

        }
        // device
    }


    private function calculateTotalRowScoreByResult($parserId, array $json)
    {
        $result = $json['result'] ?? [];

        $this->scoreInitDefaultByParser($parserId);

        if (!isset($this->total[$parserId]['time'])) {
            $this->total[$parserId]['time'] = 0;
        }
        $this->total[$parserId]['time'] += $json['time'];


        switch ($parserId) {
            case 'matomo-device-detector':
                $this->calculateRowScoreByParserMatomoDeviceDetector($parserId, $json);
                break;
        }
    }

}