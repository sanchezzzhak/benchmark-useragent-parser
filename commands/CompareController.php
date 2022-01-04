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

    /**
     *
     */
    public function actionIndex()
    {
        Console::output('Processing...');

        $total = $this->getTotalReport();

        $tableBrowser = $this->getTableBrowserNomination($total);
        $tableBot = $this->getTableBotNomination($total);
        $tableOS = $this->getTableOsNomination($total);
        $tableDevice = $this->getTableDeviceNomination($total);

        $file = __DIR__ . '/../readme.md';
        $readme = file_get_contents($file);

        $readme = preg_replace(
            '~^#{5} Bot nomination(?:.*?)\n#####~ims',
            "##### Bot nomination\n" . $tableBot . "\n#####",
            $readme, 1);

        $readme = preg_replace(
            '~^#{5} Browser nomination(?:.*?)\n#####~ims',
            "##### Browser nomination\n" . $tableBrowser . "\n#####",
            $readme, 1);

        $readme = preg_replace(
            '~^#{5} OS nomination(?:.*?)\n#####~ims',
            "##### OS nomination\n" . $tableOS . "\n#####",
            $readme, 1);

        $readme = preg_replace(
            '~^#{5} Device nomination(?:.*?)\n#####~ims',
            "##### Device nomination\n" . $tableDevice . "\n#####",
            $readme, 1);

        file_put_contents($file, $readme);

        echo json_encode($total, JSON_PRETTY_PRINT);

    }

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

    private function getTotalReport(): array
    {
        $query = BenchmarkResult::find()->with([
            'parseResults'
        ]);
        $total = [];
        foreach (ParserConfig::REPOSITORIES as $repository) {
            $this->setEmptyValues($total, $repository['id']);
        }
        $useragentCounter = 0;
        /** @var BenchmarkResult $model */
        foreach ($query->each(100) as $model) {
            $useragentCounter++;
            foreach ($model->parseResults as $result) {
                $parseId = $result->parser_id;
                $total[$parseId]['useragents'] = $useragentCounter;
                // devices
                if (!empty($result->device_type)) {
                    $aggregate = $result->device_type !== 'bot';
                    $aggregate && $total[$parseId][self::SCORE_DEVICE_TYPE]++;
                }
                if (!empty($result->brand_name)) {
                    $total[$parseId][self::SCORE_DEVICE_BRAND]++;
                }
                if (!empty($result->model_name)) {
                    $total[$parseId][self::SCORE_DEVICE_MODEL]++;
                }
                // bots
                if ($result->is_bot) {
                    $total[$parseId][self::SCORE_BOT]++;
                }
                // oss
                if (!empty($result->os_name)) {
                    $total[$parseId][self::SCORE_OS]++;
                }
                if (!empty($result->os_version)) {
                    $total[$parseId][self::SCORE_OS_VERSION]++;
                }
                // clients/browsers
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
        return $total;
    }

    /**
     * Scoring for the Device table
     * @param array $total
     * @return string
     */
    private function getTableDeviceNomination(array $total)
    {
        $deviceNomination = [];
        foreach ($total as $parserId => $row) {
            $deviceNomination[] = [
                'Parser' => ParserConfig::getNameById($parserId),
                'Count' => $row['useragents'],
                'Device type'   => $row[self::SCORE_DEVICE_TYPE],
                'Device brand' => $row[self::SCORE_DEVICE_BRAND],
                'Device model' => $row[self::SCORE_DEVICE_MODEL],
                'Scores' => $row[self::SCORE_DEVICE_TYPE] + $row[self::SCORE_DEVICE_BRAND] +  $row[self::SCORE_DEVICE_MODEL]
            ];
        }
        $deviceNomination = $this->sortByScore($deviceNomination);
        $tableOS = "| Parser Name | Count | Device types | Device brands | Device models | Scores |\n";
        $tableOS.= "| ---- | ---- | ---- | ---- | ---- | ---- |\n";
        foreach ($deviceNomination as $row) {
            $tableOS .= sprintf(
                    '| %s | %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row['Device type'],
                    $row['Device brand'],
                    $row['Device model'],
                    $row['Scores'],
                ) . PHP_EOL;
        }
        return $tableOS. PHP_EOL . PHP_EOL;
    }

    /**
     * Scoring for the OS table
     * @param array $total
     * @return string
     */
    private function getTableOsNomination(array $total)
    {
        $osNomination = [];
        foreach ($total as $parserId => $row) {
            $osNomination[] = [
                'Parser' => ParserConfig::getNameById($parserId),
                'Count' => $row['useragents'],
                'OS'   => $row[self::SCORE_OS],
                'OS Versions' => $row[self::SCORE_OS_VERSION],
                'Scores' => $row[self::SCORE_OS_VERSION] + $row[self::SCORE_OS]
            ];
        }
        $osNomination = $this->sortByScore($osNomination);
        $tableOS = "| Parser Name | Count | OS | OS Versions | Scores |\n";
        $tableOS.= "| ---- | ---- | ---- | ---- | ---- |\n";
        foreach ($osNomination as $row) {
            $tableOS .= sprintf(
                    '| %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row['OS'],
                    $row['OS Versions'],
                    $row['Scores'],
                ) . PHP_EOL;
        }

        return $tableOS. PHP_EOL . PHP_EOL;
    }

    /**
     * Scoring for the Bots table
     * @param array $total
     * @return string
     */
    private function getTableBotNomination(array $total)
    {
        $botNomination = [];
        foreach ($total as $parserId => $row) {
            $botNomination[] = [
                'Parser' => ParserConfig::getNameById($parserId),
                'Count' => $row['useragents'],
                'Bots'   => $row[self::SCORE_BOT],
                'Scores' => $row[self::SCORE_BOT]
            ];
        }
        $botNomination = $this->sortByScore($botNomination);
        $tableBot = "| Parser Name | Count | Bots | Scores |\n";
        $tableBot.= "| ---- | ---- | ---- | ---- |\n";

        foreach ($botNomination as $row) {
            $tableBot .= sprintf(
                    '| %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row['Bots'],
                    $row['Scores'],
                ) . PHP_EOL;
        }

        return $tableBot . PHP_EOL . PHP_EOL;
    }

    /**
     * Scoring for the Browser table
     * @param array $total
     * @return string
     */
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

        return $tableBrowser . PHP_EOL . PHP_EOL;
    }

    /**
     * Sorting DESC by key `Scores`
     * @param $rows
     * @return mixed
     */
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