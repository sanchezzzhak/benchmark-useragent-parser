<?php


namespace app\commands;


use app\helpers\ParserHelper;
use app\models\BenchmarkResult;
use app\models\DeviceDetectorResult;
use yii\console\Controller;
use app\helpers\ParserConfig;
use yii\helpers\Console;

class CompareController extends Controller
{
    /*
              'count' => 'count(id)',
            'minTime' => 'min(time)',
            'maxTime' => 'min(time)',
            'avgTime' => 'avg(time)',
            'totalTime' => 'sum(time)',
            'minRam' => 'min(memory)',
            'maxRam' => 'max(memory)',
            'totalRam' => 'sum(memory)',
            'avgRam' => 'avg(memory)',
            'uniqueBotFound' => 'count(DISTINCT bot_name)',
            'botFound' => 'count(is_bot)',
            'deviceTypeFound' => 'count(device_type)',
            'deviceBrandFound' => 'count(brand_name)',
            'deviceModelFound' => 'count(model_name)',
            'uniqueDeviceModelFound' => 'count(DISTINCT model_name)',
            'osFound' => 'count(os_name)',
            'osVersionFound' => 'count(os_version)',
            'clientFound' => 'count(client_name)',
            'clientTypeFound' => 'count(client_type)',
            'clientVersionFound' => 'count(client_version)',
            'engineFound' => 'count(engine_name)',
            'engineVersionFound' => 'count(engine_version)',
     */


    private const SCORE_COUNT = 'uaFound';

    private const SCORE_AVG_TIME = 'avgTime';
    private const SCORE_MAX_TIME = 'maxTime';
    private const SCORE_MIN_TIME = 'minTime';
    private const SCORE_TOTAL_TIME = 'totalTime';

    private const SCORE_AVG_RAM = 'avgRam';
    private const SCORE_MAX_RAM = 'maxRam';
    private const SCORE_MIN_RAM = 'minRam';
    private const SCORE_TOTAL_RAM = 'totalRam';


    private const SCORE_BOT = 'botFound';
    private const SCORE_BOT_UNIQUE = 'uniqueBotFound';

    private const SCORE_OS = 'osFound';
    private const SCORE_OS_VERSION = 'osVersionFound';

    private const SCORE_CLIENT = 'clientFound';
    private const SCORE_CLIENT_TYPE = 'clientTypeFound';
    private const SCORE_CLIENT_VERSION = 'clientVersionFound';
    private const SCORE_CLIENT_ENGINE = 'engineFound';
    private const SCORE_CLIENT_ENGINE_VERSION = 'engineVersionFound';

    private const SCORE_DEVICE_BRAND = 'deviceBrandFound';
    private const SCORE_DEVICE_MODEL = 'deviceModelFound';
    private const SCORE_DEVICE_TYPE = 'deviceTypeFound';
    private const SCORE_DEVICE_MODEL_UNIQUE = 'uniqueDeviceModelFound';

    /**
     *
     */
    public function actionIndex()
    {
        Console::output('Processing...');

        $total = $this->getTotalReport();

        $parsersNomination = $this->sortByScore($total);
        $columns = [
            'Parser Name',
            'UA Count',

            'Min time',
            'Max time',
            'Total time',
            'Avg time',

            'Min memory',
            'Max memory',
            'Total memory',
            'Avg memory',

            'Bots',
            'Bot uniques',

            'OS',
            'OS versions',

            'Client types',
            'Client names',
            'Client versions',
            'Engine names',
            'Engine versions',
            'Device types',
            'Brand names',
            'Model names',
            'Model unique names',
        ];
        $totalTable = "";
        $totalTable .= "| " . implode(' | ', $columns) . PHP_EOL;
        $totalTable .= "|" . str_repeat(' ---- |', count($columns)) . PHP_EOL;
        foreach ($parsersNomination as $row) {

            $count = $row[self::SCORE_COUNT];
            $row = [
                $row['Parser'],
                $count,

                $row[self::SCORE_MIN_TIME],
                $row[self::SCORE_MAX_TIME],
                round($row[self::SCORE_TOTAL_TIME], 2),
                round($row[self::SCORE_AVG_TIME], 4),

                ParserHelper::formatBytes($row[self::SCORE_MIN_RAM]) ,
                ParserHelper::formatBytes($row[self::SCORE_MAX_RAM]) ,
                ParserHelper::formatBytes($row[self::SCORE_TOTAL_RAM]) ,
                ParserHelper::formatBytes($row[self::SCORE_AVG_RAM]) ,

                $row[self::SCORE_BOT],
                $row[self::SCORE_BOT_UNIQUE],

                $this->valueWithPercent($count, $row[self::SCORE_OS]),
                $this->valueWithPercent($count, $row[self::SCORE_OS_VERSION]),

                $this->valueWithPercent($count, $row[self::SCORE_CLIENT_TYPE]),
                $this->valueWithPercent($count, $row[self::SCORE_CLIENT]),
                $this->valueWithPercent($count, $row[self::SCORE_CLIENT_VERSION]),
                $this->valueWithPercent($count, $row[self::SCORE_CLIENT_ENGINE]),
                $this->valueWithPercent($count, $row[self::SCORE_CLIENT_ENGINE_VERSION]),

                $this->valueWithPercent($count, $row[self::SCORE_DEVICE_TYPE]),
                $this->valueWithPercent($count, $row[self::SCORE_DEVICE_BRAND]),
                $this->valueWithPercent($count, $row[self::SCORE_DEVICE_MODEL]),
                $this->valueWithPercent($count, $row[self::SCORE_DEVICE_MODEL_UNIQUE]),
            ];
            $totalTable .= "|" . implode("| ", $row) . PHP_EOL;

        }
        $totalTable .= PHP_EOL . PHP_EOL;

        $file = __DIR__ . '/../readme.md';
        $readme = file_get_contents($file);

        $readme = preg_replace(
            '~^#{5} Last date scan(?:.*?)\n#####~ims',
            "##### Last date scan\n" . date('Y/m/d') . "\n#####",
            $readme, 1);

        $readme = preg_replace(
            '~^#{5} Total(?:.*?)\n#####~ims',
            "##### Total\n" . $totalTable . "\n#####",
            $readme, 1);

        file_put_contents($file, $readme);

        echo json_encode($total, JSON_PRETTY_PRINT);

    }

    private function getReportByParser(int $parserId)
    {
        $countSelect = static fn($column) => sprintf(
            "count(case when %s not in('', 'unknown') and %s not null then 1 end)",
            $column,
            $column
        );

        $q = DeviceDetectorResult::find();
        $q->select([
            'uaFound' => 'count(*)',
            'minTime' => 'min(time)',
            'maxTime' => 'max(time)',
            'avgTime' => 'avg(time)',
            'totalTime' => 'sum(time)',
            'minRam' => 'min(memory)',
            'maxRam' => 'max(memory)',
            'totalRam' => 'sum(memory)',
            'avgRam' => 'avg(memory)',
            'uniqueBotFound' => 'count(DISTINCT bot_name)',
            'botFound' => 'sum(is_bot)',
            'deviceTypeFound' => $countSelect('device_type'),
            'deviceBrandFound' => $countSelect('brand_name'),
            'deviceModelFound' =>  $countSelect('model_name'),
            'uniqueDeviceModelFound' => 'count(DISTINCT model_name)',
            'osFound' => $countSelect('os_name'),
            'osVersionFound' => $countSelect('os_version'),
            'clientFound' => $countSelect('client_name'),
            'clientTypeFound' => $countSelect('client_type'),
            'clientVersionFound' =>  $countSelect('client_version'),
            'engineFound' => $countSelect('engine_name'),
            'engineVersionFound' => $countSelect('engine_version'),
        ]);
        $q->where(['parser_id' => $parserId]);
        $q->groupBy('parser_id');
        $q->asArray();
        return $q->one();
    }


    private function getTotalReport(): array
    {
        $total = [];
        foreach (ParserConfig::REPOSITORIES as $repository) {
            $parserId = (int)$repository['id'];
            $report = $this->getReportByParser($parserId);
            $total[$parserId] = $report;
            $total[$parserId]['Parser'] = ParserConfig::getNameById($parserId);
            $total[$parserId]['Scores'] = 0;
        }


        return $total;
    }

    /**
     * Sorting DESC by key `Scores`
     * @param $rows
     * @return array
     */
    private function sortByScore($rows): array
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

    private function valueWithPercent($total, $value): string
    {
        $percent = $total / 100;
        return sprintf('%s (%s%%)', $value, round($value / $percent, 2));
    }


}