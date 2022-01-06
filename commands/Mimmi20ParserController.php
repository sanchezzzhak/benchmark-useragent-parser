<?php


namespace app\commands;


use app\components\CacheFake;
use app\components\LoggerFake;
use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;
use BrowserDetector\Detector;
use BrowserDetector\DetectorFactory;
use UaResult\Result\ResultInterface;
use yii\console\Controller;
use yii\console\ExitCode;


class Mimmi20ParserController extends Controller
{

    public function actionIndex(int $log = 0, int $skip = 0) {

        $parserId =  ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR);

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
            $this->saveParseResult($row, $parserId);

        }

        return ExitCode::OK;
    }

    private function getParser(): Detector
    {
        static $parser;
        if ($parser === null) {
            $logger = new LoggerFake;
            $cache = new CacheFake;
            $detectorFactory = new DetectorFactory($cache, $logger);
            $parser = $detectorFactory();
        }
        return $parser;
    }

    /**
     * @param BenchmarkResult $row
     * @param int $parserId
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function saveParseResult(BenchmarkResult $row, int $parserId): bool
    {
        $parser = $this->getParser();
        $useragent = $row->user_agent;
        /** @var ResultInterface $result */
        $result = null;
        $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent, &$result) {
            $result = $parser($useragent);
        });

        $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
        $model->time = $info['time'];
        $model->memory = $info['memory'];
        $model->is_bot = false;

        $browser = $result->getBrowser();
        $deviceResult = $result->toArray();

        if(isset($deviceResult['headers'])) {
            unset($deviceResult['headers']);
        }

        if ($browser->getType()->isBot()) {
            $model->is_bot = true;
            $model->data_json = json_encode($deviceResult);
            $model->bot_name = $deviceResult['browser']['name'] ?? '';
            return $model->save();
        }

        $model->os_name  = $deviceResult['os']['name'] ?? '';
        $model->os_version = $deviceResult['os']['version'] ?? '';
        $model->engine_version = $deviceResult['engine']['version'] ?? '';
        $model->engine_name = $deviceResult['engine']['name'] ?? '';

        $model->client_name = $deviceResult['browser']['name'] ?? '';
        $model->client_version = $deviceResult['browser']['version'] ?? '';
        $model->client_type = $deviceResult['browser']['type'] ?? '';

        $model->device_type = $deviceResult['device']['type'] ?? '';
        $model->brand_name = $deviceResult['device']['brand'] ?? '';
        $model->model_name = $deviceResult['device']['marketingName'] ?? '';
        $model->data_json = json_encode($deviceResult);
        return $model->save();
    }

}