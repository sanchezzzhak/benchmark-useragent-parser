<?php


namespace app\commands;

use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;

use foroco\BrowserDetection;
use yii\console\Controller;
use yii\console\ExitCode;

class ForocoParserController extends Controller
{
    /**
     * @param int $log
     * @param int $skip
     * @return int
     */
    public function actionIndex(int $log = 0, int $skip = 0)
    {
        $parserId =  ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_FOROCO_BROWSERDETECTION);

        $count = BenchmarkResult::find()->count();
        $perPage = 500;
        $totalPages = ceil($count / $perPage);

        $this->stdout(sprintf('Total useragents %s', $count) . PHP_EOL);

        /** @var BenchmarkResult $row */
        for ($i = 0; $i < $totalPages; $i++) {
            $offset = $i * $perPage;
            $rows = BenchmarkResult::find()->limit($perPage)->offset($offset)->all();
            $this->stdout(sprintf('%s/%s', $offset, $count) . PHP_EOL);
            foreach ($rows as $row) {
                $useragent = $row->user_agent;
                $log && $this->stdout(sprintf('#%s parse %s', $row->id, $useragent) . PHP_EOL);
                $this->saveParseResult($row, $parserId);
            }
        }
        return ExitCode::OK;
    }

    private function getParser(): BrowserDetection
    {
        static $parser;
        if ($parser === null) {
            $parser = new BrowserDetection;
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
        $result = [];
        $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent, &$result) {
           try {
               $result = $parser->getAll($useragent);
           } catch (\Exception $exception) {
               echo $useragent . PHP_EOL;
               echo $exception->getMessage() , ' in Line ' , $exception->getLine(),
                   ' in File' . $exception->getFile() . PHP_EOL;
               $result = [];
           }
        });

        $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
        $model->time = $info['time'];
        $model->memory = $info['memory'];
        $model->device_type = $result['device_type'] ?? null;

        $model->os_name = $result['os_name'] ?? null;
        $model->os_version = !empty($result['os_version']) ? (string)$result['os_version']: null;

        $model->client_name = $result['browser_name'] ?? null;
        $model->client_version = !empty($result['browser_version']) ? (string)$result['browser_version']: null;

        $model->data_json = json_encode($result);
        if(!$model->save()) {
            var_dump($model->getErrors());
        }
        return false;
    }
}