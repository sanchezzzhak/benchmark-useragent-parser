<?php


namespace app\commands;

use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;

use Mobile_Detect;
use yii\console\Controller;
use yii\console\ExitCode;

class MobiledetectlibParserController extends Controller
{
    /**
     * @param int $log
     * @param int $skip
     * @return int
     */
    public function actionIndex(int $log = 0, int $skip = 0)
    {
        $parserId =  ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_MOBILEDETECTLIB);

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

    private function getParser(): Mobile_Detect
    {
        static $parser;
        if ($parser === null) {
            $parser = new Mobile_Detect;
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
        $result = null;
        $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent, &$result) {
             $parser->setUserAgent($useragent);

             $isBot = $parser->isBot() || $parser->isMobileBot();

            if ($isMobile = $parser->isMobile()) {
                $deviceType = 'mobile';
            } else if ($parser->isTablet()) {
                $deviceType = 'tablet';
            } else {
                $deviceType = 'desktop';
            }
            $result = [
                'device_type' => $deviceType,
                'is_bot' => $isBot
            ];
        });

        var_dump($result, $parser->getMatchingRegex(), $parser->getMatchesArray());


        $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
        $model->time = $info['time'];
        $model->memory = $info['memory'];
        $model->is_bot = $result['is_bot'];

        $model->data_json = json_encode($result);
        return $model->save();
    }
}