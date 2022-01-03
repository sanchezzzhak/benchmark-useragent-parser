<?php


namespace app\commands;


use app\models\BenchmarkResult;
use app\models\Offer;
use yii\console\Controller;
use app\helpers\ParserConfig;
use yii\console\Markdown;
use yii\console\widgets\Table;
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

                if (!empty($result->brand_name)) {
                    $total[$parseId][self::SCORE_DEVICE_BRAND]++;
                }
                if (!empty($result->device_type)) {
                    $total[$parseId][self::SCORE_DEVICE_TYPE]++;
                }
                if ($result->is_bot) {
                    $total[$parseId][self::SCORE_BOT]++;
                }
                if (!empty($result->os_name)) {
                    $total[$parseId][self::SCORE_OS]++;
                }
                if (!empty($result->os_version)) {
                    $total[$parseId][self::SCORE_OS_VERSION]++;
                }
                if (!empty($result->client_name)) {
                    $total[$parseId][self::SCORE_BROWSER]++;
                }
                if (!empty($result->client_version)) {
                    $total[$parseId][self::SCORE_BROWSER_VERSION]++;
                }
                if (!empty($result->engine_name)) {
                    $total[$parseId][self::SCORE_BROWSER_ENGINE]++;
                }
                if (!empty($result->engine_version)) {
                    $total[$parseId][self::SCORE_BROWSER_ENGINE_VERSION]++;
                }
            }
        }

        $tableBrowser = $this->getTableBrowserNomination($total);

        $file = __DIR__ . '/../readme.md';
        $readme = file_get_contents($file);
        $readme = preg_replace(
            '~^#{5} Browser nomination(?:.*?)\n#####~ims',
            "##### Browser nomination\n" . $tableBrowser . "\n#####",
            $readme, 1);

        file_put_contents($file, $readme);
    }

    private function getTableBrowserNomination(array $total)
    {
        $browserNomination = [];
        foreach ($total as $parserId => $row) {
            $browserNomination[] = [
                'Parser' => ParserConfig::getNameById($parserId),
                'Count' => $row['useragents'],
                'Browsers'   => $row[self::SCORE_BROWSER],
                'Versions'   => $row[self::SCORE_BROWSER_VERSION],
                'Engines'   => $row[self::SCORE_BROWSER_ENGINE],
                'Scores' => $row[self::SCORE_BROWSER] + $row[self::SCORE_BROWSER_VERSION] + $row[self::SCORE_BROWSER_ENGINE]
            ];
        }
        $browserNomination = $this->sortByScore($browserNomination);

        $tableBrowser = "| Parser Name | Count | Browsers | Versions | Engines | Scores |\n";
        $tableBrowser.= "| ---- | ---- | ---- | ---- | ---- | ---- |\n";

        foreach ($browserNomination as $row) {
            $tableBrowser .= sprintf(
                    '| %s | %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row['Browsers'],
                    $row['Versions'],
                    $row['Engines'],
                    $row['Scores'],
                ) . PHP_EOL;
        }

        $tableBrowser.= PHP_EOL;
        $tableBrowser.= PHP_EOL;

        return $tableBrowser;
    }

    private function sortByScore($rows)
    {
        uasort($rows, static function ($a, $b) {
            $posA = (int)($a['Scores'] ?? 0);
            $posB = (int)($b['Scores'] ?? 0);
            if($posA === $posB) {
                return 0;
            }
            return $posA < $posB ? 1: -1;
        });
        return $rows;
    }


}