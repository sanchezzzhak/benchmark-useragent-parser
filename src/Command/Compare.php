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

    private const SCORE_BOT = 'bots';

    private const SCORE_OS = 'os';
    private const SCORE_OS_VERSION = 'osVersion';
    private const SCORE_OS_PLATFORM = 'osPlatform';

    private const SCORE_BROWSER = 'browser';
    private const SCORE_BROWSER_VERSION = 'browserVersion';
    private const SCORE_BROWSER_ENGINE = 'browserEngine';
    private const SCORE_BROWSER_ENGINE_VERSION = 'browserEngineVersion';

    private const SCORE_DEVICE_BRAND = 'deviceBrand';
    private const SCORE_DEVICE_MODEL = 'deviceModel';
    private const SCORE_DEVICE_TYPE = 'deviceType';


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
        $saveTotalPath = $reportPath . DIRECTORY_SEPARATOR . 'total.json';

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

        $this->saveTotal($saveTotalPath);
    }

    private function saveTotal($saveTotalPath): void
    {
        /** @var ParserHelper $parserHelper */
        $parserHelper = $this->getHelper('parser');

        // set count useragent iterations and others data
        foreach ($this->total ?? [] as $parserId => $total) {
            $this->total[$parserId]['useragents'] = $this->iterate;

            $this->total[$parserId]['timeAverage'] = round($this->total[$parserId]['time']/  $this->iterate , 4);
            $this->total[$parserId]['memoryAverage'] = $parserHelper->formatBytes($this->total[$parserId]['memory']/$this->iterate);

            $this->total[$parserId]['time'] = round($this->total[$parserId]['time'], 4);
            $this->total[$parserId]['memoryMax'] = $parserHelper->formatBytes($this->total[$parserId]['memoryMax']);


        }

        /** @var ParserHelper $parserHelper */



        file_put_contents($saveTotalPath, json_encode($this->total));
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
                $reportData[$handleParserId]['time'] = $json['time'];

                $this->aggregateScoreByRow($handleParserId, $json);

                $reportData[$handleParserId]['memory'] = $parserHelper->formatBytes($json['memory']);

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

        $this->total[$parserId]['time'] = 0;
        $this->total[$parserId]['memory'] = 0;
        $this->total[$parserId]['memoryMax'] = 0;
        $this->total[$parserId]['timeMax'] = 0;

        $attrTypes = [
            self::SCORE_BOT,
            self::SCORE_OS,
            self::SCORE_OS_VERSION,
            self::SCORE_OS_PLATFORM,
            self::SCORE_BROWSER,
            self::SCORE_BROWSER_VERSION,
            self::SCORE_BROWSER_ENGINE,
            self::SCORE_BROWSER_ENGINE_VERSION,
            self::SCORE_DEVICE_TYPE,
            self::SCORE_DEVICE_BRAND,
            self::SCORE_DEVICE_MODEL,
        ];
        foreach ($attrTypes as $type) {
            $this->scoreDefaultValueByType($type, $parserId);
        }
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

    /**
     * @param array $data
     * @param array $attrs
     * @param string $parserId
     */
    private function eachScoreIncrByAttrs($data, $attrs, $parserId): void
    {
        foreach ($attrs as $typeId => $attr) {
            $attrValue = $data[$attr] ?? null;
            if (!is_array($attrValue) && (string)$attrValue !== '' && $attrValue !== 'unknown') {
                $this->scoreIncrByType($typeId, $parserId);
            }
        }
    }

    /**
     * @param $parserId
     * @param array $json
     */
    private function aggregateScoreByParserMatomoDeviceDetector($parserId, array $json)
    {
        $result = $json['result'] ?? [];
        $botName = $result['bot']['name'] ?? null;
        if ((string)$botName !== '') {
            $this->scoreIncrByType(self::SCORE_BOT, $parserId);
            return;
        }
        // os
        if (isset($result['os']) && is_array($result['os'])) {
            $this->eachScoreIncrByAttrs($result['os'], [
                self::SCORE_OS => 'name',
                self::SCORE_OS_VERSION => 'version',
                self::SCORE_OS_PLATFORM => 'platform'
            ], $parserId);
        }
        // client:  browser, mobile app etc
        if (isset($result['client']) && is_array($result['client'])) {
            $type = $result['client']['type'] ?? null;
            if ((string)$type !== '') {
                $type === 'browser' ? $this->eachScoreIncrByAttrs($result['client'], [
                    self::SCORE_BROWSER => 'name',
                    self::SCORE_BROWSER_VERSION => 'version',
                    self::SCORE_BROWSER_ENGINE => 'engine',
                    self::SCORE_BROWSER_ENGINE_VERSION => 'engine_version',
                ], $parserId) : null;

            }
        }
        // device
        if (isset($result['device']) && is_array($result['device'])) {
            $this->eachScoreIncrByAttrs($result['device'], [
                self::SCORE_DEVICE_BRAND => 'brand',
                self::SCORE_DEVICE_MODEL => 'model',
                self::SCORE_DEVICE_TYPE => 'type'
            ], $parserId);
        }

    }

    private function aggregateScoreByParserMimmi20BrowserDetector($parserId, array $json)
    {
        $result = $json['result'] ?? [];
        $clientType = $result['client']['type'] ?? '';
        if ((string)$clientType === 'bot') {
            $this->scoreIncrByType(self::SCORE_BOT, $parserId);
            return;
        }

        if (isset($result['os']) && is_array($result['os'])) {
            $this->eachScoreIncrByAttrs($result['os'], [
                self::SCORE_OS => 'name',
                self::SCORE_OS_VERSION => 'version',
                self::SCORE_OS_PLATFORM => 'bits' // todo check
            ], $parserId);
        }
        if (isset($result['client']) && is_array($result['client'])) {
            (string)$clientType === 'browser'
                ? $this->eachScoreIncrByAttrs($result['client'], [
                self::SCORE_BROWSER => 'name',
                self::SCORE_BROWSER_VERSION => 'version',
            ], $parserId) : null;
        }

        if (isset($result['device']) && is_array($result['device'])) {
            $this->eachScoreIncrByAttrs($result['device'], [
                self::SCORE_DEVICE_BRAND => 'brand',
                self::SCORE_DEVICE_MODEL => 'deviceName',
                self::SCORE_DEVICE_TYPE => 'type'
            ], $parserId);
        }
    }

    private function aggregateScoreByParserWhichBrowserParser($parserId, array $json)
    {
        $result = $json['result'] ?? [];

//        var_dump($result);
//
        $deviceType = $result['device']['type'] ?? '';
        if ((string)$deviceType === 'bot') {
            $this->scoreIncrByType(self::SCORE_BOT, $parserId);
            return;
        }

        if (isset($result['os']) && is_array($result['os'])) {
            $this->eachScoreIncrByAttrs($result['os'], [
                self::SCORE_OS => 'name',
                self::SCORE_OS_VERSION => 'version',
            ], $parserId);
        }

        if (isset($result['browser']) && is_array($result['browser'])) {
            $browserType = $result['browser']['type'] ?? null;

            if ((string)$browserType === 'browser') {
                $this->eachScoreIncrByAttrs($result['browser'], [
                    self::SCORE_BROWSER => 'name',
                    self::SCORE_BROWSER_VERSION => 'version',
                ], $parserId);

                if (isset($result['engine']) && is_array($result['engine'])) {
                    $this->eachScoreIncrByAttrs($result['engine'], [
                        self::SCORE_BROWSER_ENGINE => 'name',
                        self::SCORE_BROWSER_ENGINE_VERSION => 'version',
                    ], $parserId);
                }
            }
        }

        if (isset($result['device']) && is_array($result['device'])) {
            $this->eachScoreIncrByAttrs($result['device'], [
                self::SCORE_DEVICE_BRAND => 'manufacturer',
                self::SCORE_DEVICE_MODEL => 'model',
                self::SCORE_DEVICE_TYPE => 'type'
            ], $parserId);
        }
    }

    private function aggregateScoreByRow($parserId, array $json)
    {
        $this->scoreInitDefaultByParser($parserId);

        $this->total[$parserId]['time'] += $json['time'];
        $this->total[$parserId]['memory'] += $json['memory'];

        if ($this->total[$parserId]['timeMax'] === 0 || $this->total[$parserId]['timeMax'] < $json['time']) {
            $this->total[$parserId]['timeMax'] = $json['time'];
        }
        if ($this->total[$parserId]['memoryMax'] === 0 || $this->total[$parserId]['memoryMax'] < $json['memory']) {
            $this->total[$parserId]['memoryMax'] = $json['memory'];
        }

        switch ($parserId) {
            case 'matomo-device-detector':
                $this->aggregateScoreByParserMatomoDeviceDetector($parserId, $json);
                break;
            case 'mimmi20-browser-detector':
                $this->aggregateScoreByParserMimmi20BrowserDetector($parserId, $json);
                break;
            case 'whichbrowser-parser':
                $this->aggregateScoreByParserWhichBrowserParser($parserId, $json);
                break;
        }
    }

}