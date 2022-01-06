<?php


namespace app\commands;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;
use app\helpers\Benchmark;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;
use BrowscapPHP\Browscap;
use Monolog\Logger;
use yii\console\ExitCode;
use yii\console\Controller;

class BrowsercapParserController extends Controller
{
    public function actionIndex(int $log = 0) {

        $parserId =  ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_BROWSCAP);


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

    private function getParser(): Browscap
    {
        static $parser;
        if ($parser === null) {

            $dir        = __DIR__ . "/../vendor/browscap/browscap-php/resources";
            $adapter    = new LocalFilesystemAdapter($dir);
            $filesystem = new Filesystem($adapter);
            $cache      = new SimpleCache(
                new Flysystem($filesystem)
            );
            $logger = new Logger('name');
            $parser = new Browscap($cache, $logger);
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
            $result = $parser->getBrowser($useragent);
        });

        $model = DeviceDetectorResult::findOrCreate($row->id, $parserId);
        $model->time = $info['time'];
        $model->memory = $info['memory'];
        $model->is_bot = $result->crawler ?? false;
        if ($model->is_bot) {
            $model->bot_name = $result->browser ?? null;
            $model->data_json = json_encode($result);
            return $model->save();
        }

        $model->client_name = $result->browser ?? null;
        $model->client_version = $result->version ?? null;

        $model->os_name = $result->platform ?? null;
        if ($result->platform_version !== null && $result->platform_version === 'unknown') {
            $model->os_version = (string)($result->platform_version ?? null);
        }

        $model->device_type = $result->device_type !== 'unknown' ? $result->device_type: null;
        $model->brand_name = $result->device_name ?? null;
        if ($model->brand_name === 'unknown') {
            $model->brand_name = $result->device_brand_name ?? null;
        }
        $model->model_name = $result->device_code_name ?? null;

        $model->data_json = json_encode($result);
        return $model->save();
    }
}