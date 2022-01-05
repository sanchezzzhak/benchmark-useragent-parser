<?php

namespace app\commands;

use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\OperatingSystem;
use yii\console\ExitCode;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class MatomoParserController
 * @package app\commands
 */
class MatomoParserController extends Controller
{
    private function getParser(): DeviceDetector
    {
        static $parser;
        if ($parser === null) {
            $parser = new DeviceDetector();
        }
        return $parser;
    }

    /**
     * @param int $log
     * @param int $skip
     * @return int
     */
    public function actionIndex(int $log = 0, $skip = 0)
    {
        $parserId =  ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR);

        $query = BenchmarkResult::find();
        $queryCount = clone $query;
        $count = $queryCount->count();

        $this->stdout(sprintf('Total useragents %s', $count) . PHP_EOL);

        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);


        /** @var BenchmarkResult $row */
        $i = 0;
        foreach ($query->each() as $row) {
            $i++;
            if ($skip > $i) {
                continue;
            }
            if ($i % 100 === 0) {
                $this->stdout(sprintf('%s/%s', $i, $count) . PHP_EOL);
            }

            $useragent = $row->user_agent;
            $log && $this->stdout(sprintf('#%s parse %s', $row->id, $useragent) . PHP_EOL);
            $this->saveParseResult($row, $parserId);
        }

        return ExitCode::OK;
    }

    /**
     * @param BenchmarkResult $row
     * @param int $parserId
     * @return bool
     */
    public function saveParseResult(BenchmarkResult $row, int $parserId): bool
    {
        $parser = $this->getParser();
        $useragent = $row->user_agent;
        $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent) {
            $parser->setUserAgent($useragent);
            $parser->parse();
        });
        $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
        $model->time = $info['time'];
        $model->memory = $info['memory'];
        $model->is_bot = false;

        if ($parser->isBot()) {
            $detectResult = $parser->getBot();
            if (!is_array($detectResult)) {
                return false;
            }

            $jsonData = json_encode($detectResult);
            $model->bot_name = $detectResult['name'] ?? '';
            $model->is_bot = true;
            $model->data_json = $jsonData;
            return $model->save();
        }
        $osFamily = OperatingSystem::getOsFamily($parser->getOs('name')) ?? '';
        $browserFamily = Browser::getBrowserFamily($parser->getClient('name')) ?? '';

        $osData = $parser->getOs();
        $clientData = $parser->getClient();
        unset($osData['short_name']);
        unset($clientData['short_name']);

        $detectResult = [
            'os' => $osData,
            'client' => $clientData,
            'device' => [
                'type' => $parser->getDeviceName(),
                'brand' => $parser->getBrandName(),
                'model' => $parser->getModel(),
            ],
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
        ];
        $jsonData = json_encode($detectResult);

        $model->os_name  = $osData['name'] ?? '';
        $model->os_version = $osData['version'] ?? '';

        if(isset($clientData['engine'])) {
            $model->engine_version = $clientData['engine_version'];
            $model->engine_name = $clientData['engine'];
        }

        $model->client_name = $clientData['name'] ?? '';
        $model->client_version = $clientData['version'] ?? '';
        $model->client_type = $clientData['type'] ?? '';

        $model->device_type = $detectResult['device']['type'];
        $model->brand_name = $detectResult['device']['brand'];
        $model->model_name = $detectResult['device']['model'];
        $model->data_json = $jsonData;

        return $model->save();
    }

}