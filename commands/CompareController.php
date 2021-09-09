<?php


namespace app\commands;


use app\models\BenchmarkResult;
use yii\console\Controller;
use app\helpers\ParserConfig;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class CompareController extends Controller
{
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

    private function setEmptyValues(&$total, $parserId) {
        $total[$parserId] = [];
        $total[$parserId]['useragents'] = 0;
        $total[$parserId]['time'] = 0;
        $total[$parserId]['memory'] = 0;
        $total[$parserId]['memoryMax'] = 0;
        $total[$parserId]['timeMax'] = 0;

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
            if (!isset($this->total[$parserId][$type])) {
                $total[$parserId][$type] = 0;
            }
        }
    }

    public function actionIndex()
    {
        $query = BenchmarkResult::find()->with([
            'parseResults'
        ]);

        $total = [];
        foreach (ParserConfig::REPOSITORIES as $repository) {
            $this->setEmptyValues($total, $repository['id']);
        }
        $useragentCounter = 0;

        Console::output('Processing...');


        /** @var BenchmarkResult $model */
        foreach ($query->each(100) as $model) {
            $useragentCounter++;
            foreach ($model->parseResults as $result) {
                $parseId = $result->parser_id;
                $total[$parseId]['useragents'] = $useragentCounter;
            }

        }
        var_dump($total);

    }


}