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

    private const SCORE_CLIENT = 'client';
    private const SCORE_CLIENT_BROWSER = 'browser';
    private const SCORE_CLIENT_TYPE = 'clientType';
    private const SCORE_CLIENT_VERSION = 'browserVersion';
    private const SCORE_CLIENT_ENGINE = 'browserEngine';
    private const SCORE_CLIENT_ENGINE_VERSION = 'browserEngineVersion';

    private const SCORE_DEVICE_BRAND = 'deviceBrand';
    private const SCORE_DEVICE_MODEL = 'deviceModel';
    private const SCORE_DEVICE_TYPE = 'deviceType';
    private const SCORE_DEVICE_TYPE_SMARTPHONE = 'deviceTypeSmartphone';
    private const SCORE_DEVICE_TYPE_FEATURE_PHONE = 'deviceTypeFeaturePhone';
    private const SCORE_DEVICE_TYPE_TABLET = 'deviceTypeTablet';
    private const SCORE_DEVICE_TYPE_TV = 'deviceTypeTv';

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

    private function setEmptyValues(&$total, $parserId)
    {
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
            self::SCORE_CLIENT,
            self::SCORE_CLIENT_TYPE,
            self::SCORE_CLIENT_BROWSER,
            self::SCORE_CLIENT_VERSION,
            self::SCORE_CLIENT_ENGINE,
            self::SCORE_CLIENT_ENGINE_VERSION,
            self::SCORE_DEVICE_TYPE,
            self::SCORE_DEVICE_TYPE_SMARTPHONE,
            self::SCORE_DEVICE_TYPE_FEATURE_PHONE,
            self::SCORE_DEVICE_TYPE_TABLET,
            self::SCORE_DEVICE_TYPE_TV,
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
                $data = json_decode($result->data_json, true);

                if (!empty($result->device_type)) {
                    $subType = $data['device']['subtype'] ?? null;
                    $aggregate = !in_array($result->device_type, ['unknown', 'bot']);
                    $aggregate && $total[$parseId][self::SCORE_DEVICE_TYPE]++;

                    if (in_array($result->device_type, ['tv', 'television'])) {
                        $total[$parseId][self::SCORE_DEVICE_TYPE_TV]++;
                    }
                    if (in_array($result->device_type, ['tablet'])) {
                        $total[$parseId][self::SCORE_DEVICE_TYPE_TABLET]++;
                    }

                    $hasSmartphone = $result->device_type === 'smartphone'
                        || ($result->device_type === 'mobile' && $subType === 'smart');
                    if ($hasSmartphone) {
                        $total[$parseId][self::SCORE_DEVICE_TYPE_SMARTPHONE]++;
                    }

                    $hasFeaturePhone = in_array($result->device_type, ['feature-phone', 'feature phone'])
                        || ($result->device_type === 'mobile' && $subType === 'feature');
                    if ($hasFeaturePhone) {
                        $total[$parseId][self::SCORE_DEVICE_TYPE_FEATURE_PHONE]++;
                    }
                }

                if (!empty($result->brand_name)) {
                    $aggregate = !in_array($result->brand_name, ['unknown']);
                    $aggregate && $total[$parseId][self::SCORE_DEVICE_BRAND]++;
                }

                if (!empty($result->model_name)) {
                    $aggregate = !in_array($result->model_name, ['unknown', 'general Mobile Phone', 'general Tablet']);
                    $aggregate && $total[$parseId][self::SCORE_DEVICE_MODEL]++;
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
                    $total[$parseId][self::SCORE_CLIENT]++;
                }
                if (!empty($result->client_type)) {
                    $aggregate = !in_array($result->client_type, ['unknown']);
                    $aggregate && $total[$parseId][self::SCORE_CLIENT_TYPE]++;

                    if (strtolower($result->client_type) === 'browser') {
                        $total[$parseId][self::SCORE_CLIENT_BROWSER]++;
                    }
                }
                if (!empty($result->client_version)) {
                    $total[$parseId][self::SCORE_CLIENT_VERSION]++;
                }
                if (!empty($result->engine_name)) {
                    $total[$parseId][self::SCORE_CLIENT_ENGINE]++;
                }
                if (!empty($result->engine_version)) {
                    $total[$parseId][self::SCORE_CLIENT_ENGINE_VERSION]++;
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
                self::SCORE_DEVICE_TYPE => $row[self::SCORE_DEVICE_TYPE],
                self::SCORE_DEVICE_BRAND => $row[self::SCORE_DEVICE_BRAND],
                self::SCORE_DEVICE_MODEL => $row[self::SCORE_DEVICE_MODEL],
                self::SCORE_DEVICE_TYPE_SMARTPHONE => $row[self::SCORE_DEVICE_TYPE_SMARTPHONE],
                self::SCORE_DEVICE_TYPE_TABLET => $row[self::SCORE_DEVICE_TYPE_TABLET],
                self::SCORE_DEVICE_TYPE_FEATURE_PHONE => $row[self::SCORE_DEVICE_TYPE_FEATURE_PHONE],
                'Scores' => $row[self::SCORE_DEVICE_TYPE] + $row[self::SCORE_DEVICE_BRAND] + $row[self::SCORE_DEVICE_MODEL]
            ];
        }
        $deviceNomination = $this->sortByScore($deviceNomination);
        $tableOS = "| Parser Name | Count | Device types | Smartphones | Tables | Feature phones | Device brands | Device models | Scores |\n";
        $tableOS .= "| ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- | ---- |\n";
        foreach ($deviceNomination as $row) {
            $tableOS .= sprintf(
                    '| %s | %s | %s | %s | %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row[self::SCORE_DEVICE_TYPE],
                    $row[self::SCORE_DEVICE_TYPE_SMARTPHONE],
                    $row[self::SCORE_DEVICE_TYPE_TABLET],
                    $row[self::SCORE_DEVICE_TYPE_FEATURE_PHONE],
                    $row[self::SCORE_DEVICE_BRAND],
                    $row[self::SCORE_DEVICE_MODEL],
                    $row['Scores'],
                ) . PHP_EOL;
        }
        return $tableOS . PHP_EOL . PHP_EOL;
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
                self::SCORE_OS => $row[self::SCORE_OS],
                self::SCORE_OS_VERSION => $row[self::SCORE_OS_VERSION],
                'Scores' => $row[self::SCORE_OS_VERSION] + $row[self::SCORE_OS]
            ];
        }
        $osNomination = $this->sortByScore($osNomination);
        $tableOS = "| Parser Name | Count | OS | OS Versions | Scores |\n";
        $tableOS .= "| ---- | ---- | ---- | ---- | ---- |\n";
        foreach ($osNomination as $row) {
            $tableOS .= sprintf(
                    '| %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row[self::SCORE_OS],
                    $row[self::SCORE_OS_VERSION],
                    $row['Scores'],
                ) . PHP_EOL;
        }

        return $tableOS . PHP_EOL . PHP_EOL;
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
                self::SCORE_BOT => $row[self::SCORE_BOT],
                'Scores' => $row[self::SCORE_BOT]
            ];
        }
        $botNomination = $this->sortByScore($botNomination);
        $tableBot = "| Parser Name | Count | Bots | Scores |\n";
        $tableBot .= "| ---- | ---- | ---- | ---- |\n";

        foreach ($botNomination as $row) {
            $tableBot .= sprintf(
                    '| %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row[self::SCORE_BOT],
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
                self::SCORE_CLIENT => $row[self::SCORE_CLIENT],
                self::SCORE_CLIENT_BROWSER => $row[self::SCORE_CLIENT_BROWSER],
                self::SCORE_CLIENT_VERSION => $row[self::SCORE_CLIENT_VERSION],
                self::SCORE_CLIENT_ENGINE => $row[self::SCORE_CLIENT_ENGINE],
                'Scores' => $row[self::SCORE_CLIENT]
                    + $row[self::SCORE_CLIENT_BROWSER]
                    + $row[self::SCORE_CLIENT_VERSION]
                    + $row[self::SCORE_CLIENT_ENGINE]
            ];
        }
        $browserNomination = $this->sortByScore($browserNomination);

        $tableBrowser = "| Parser Name | Count | Clients | Browsers | Versions | Engines | Scores |\n";
        $tableBrowser .= "| ---- | ---- | ---- | ---- | ---- | ---- | ---- |\n";

        foreach ($browserNomination as $row) {
            $tableBrowser .= sprintf(
                    '| %s | %s | %s | %s | %s | %s | %s |',
                    $row['Parser'],
                    $row['Count'],
                    $row[self::SCORE_CLIENT],
                    $row[self::SCORE_CLIENT_BROWSER],
                    $row[self::SCORE_CLIENT_VERSION],
                    $row[self::SCORE_CLIENT_ENGINE],
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
            if ($posA === $posB) {
                return 0;
            }
            return $posA < $posB ? 1 : -1;
        });
        return $rows;
    }


}