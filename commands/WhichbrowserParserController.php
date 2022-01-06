<?php


namespace app\commands;

use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;
use yii\console\Controller;
use yii\console\ExitCode;
use WhichBrowser\Parser;

class WhichbrowserParserController extends Controller
{

    public function actionIndex(int $log = 0, int $skip = 0)
    {
        $parserId = ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_WHICHBROWSER_PARSER
        );
        $query = BenchmarkResult::find();
        $queryCount = clone $query;
        $count = $queryCount->count();

        $this->stdout(sprintf('Total useragents %s', $count) . PHP_EOL);

        $i =0;
        /** @var BenchmarkResult $row */
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

            $parser = null;
            $useragentHeader = 'UserAgent: ' . $useragent;
            $info = Benchmark::benchmarkWithCallback(function () use (&$parser, $useragentHeader) {
                $parser = new Parser($useragentHeader, ['detectBots' => true]);
            });

            $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
            $model->time = $info['time'];
            $model->memory = $info['memory'];
            $model->is_bot = false;

            $detectResult = [
                'browser' => $parser->browser->toArray(),
                'engine' => $parser->engine->toArray(),
                'os' => $parser->os->toArray(),
                'device' => $parser->device->toArray()
            ];

            $deviceType = $detectResult['device']['type'] ?? '';
            $deviceSubType = $detectResult['device']['subtype'] ?? '';
            $model->device_type = $deviceSubType !== '' ? sprintf('%s-%s', $deviceSubType, $deviceType) : $deviceType;
            $model->data_json = json_encode($detectResult);

            if ($deviceType === 'bot') {
                $model->is_bot = true;
                $model->bot_name  = $detectResult['browser']['name'] ?? '';
                $model->save();
                continue;
            }

            $model->client_name = $detectResult['browser']['name'] ?? '';
            $model->client_type = $detectResult['browser']['type'] ?? '';
            if (isset($detectResult['browser']['version']) && is_array($detectResult['browser']['version'])) {
                $model->client_version = $detectResult['browser']['version']['value'] ?? '';
            } else {
                $model->client_version = $detectResult['browser']['version'] ?? '';
            }

            $model->os_name = $detectResult['os']['name'] ?? '';
            $model->os_version = $detectResult['os']['version'] ?? '';

            $model->engine_name = $detectResult['engine']['name'] ?? '';
            $model->engine_version = $detectResult['engine']['version'] ?? '';

            $model->brand_name = $detectResult['device']['manufacturer'] ?? '';
            $model->model_name = $detectResult['device']['model'] ?? '';
            $model->save();
        }

        return ExitCode::OK;
    }




}