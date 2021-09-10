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

    public function actionIndex($log = false)
    {
        $parserId = ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_WHICHBROWSER_PARSER
        );
        $query = BenchmarkResult::find();

        /** @var BenchmarkResult $row */
        foreach ($query->each() as $row) {
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

            $detectResult = [
                'browser' => $parser->browser->toArray(),
                'engine' => $parser->engine->toArray(),
                'os' => $parser->os->toArray(),
                'device' => $parser->device->toArray()
            ];

            $deviceType = $detectResult['device']['type'] ?? '';
            $model->device_type = $deviceType;
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

            $model->engine_name = $detectResult['engine']['name'] ?? '';
            $model->engine_version = $detectResult['engine']['version'] ?? '';

            $model->brand_name = $detectResult['device']['manufacturer'] ?? '';
            $model->model_name = $detectResult['device']['model'] ?? '';
            $model->save();
        }

        return ExitCode::OK;
    }




}