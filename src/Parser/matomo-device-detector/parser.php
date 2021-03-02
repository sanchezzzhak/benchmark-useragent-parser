<?php

require_once __DIR__ . '/../../Helpers/Benchmark.php';
require_once __DIR__ . '/../../Repository/matomo/device-detector/vendor/autoload.php';
require_once __DIR__ . '/../../functions.php';

use App\Helpers\Benchmark;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

webNotSupportedRunning();

$options = getopt(null, [
    "fixtures::", // use paths fixtures useragents
    "files::",    // use files useragents
    "report::",   // set custom report name
    "date::"      // set date mark report
]);
$fixtureRawPath = $options['fixtures'] ?? null;
$fileRawPath = $options['files'] ?? null;
$reportName = $options['report'] ?? 'default';

if ($fileRawPath === null && $fixtureRawPath === null) {
    throw new InvalidArgumentException('args: fixtures or files not required');
}

function createReport(string $useragent, string $reportPath): void
{
    static $parser;
    if (!$parser) {
        $parser = new DeviceDetector();
    }

    $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent) {
        $parser->setUserAgent($useragent);
        $parser->parse();
    });

    if ($parser->isBot()) {
        $report = array_merge([
            'user_agent' => $useragent,
            'result' => [
                'bot' => $parser->getBot(),
            ],
        ], $info);
        file_put_contents($reportPath, json_encode($report) . PHP_EOL, FILE_APPEND);
    }

    $osFamily = OperatingSystem::getOsFamily($parser->getOs('name')) ?? '';
    $browserFamily = Browser::getBrowserFamily($parser->getClient('name')) ?? '';

    $report = array_merge([
        'user_agent' => $useragent,
        'result' => [
            'os' => $parser->getOs(),
            'client' => $parser->getClient(),
            'device' => [
                'type' => $parser->getDeviceName(),
                'brand' => $parser->getBrandName(),
                'model' => $parser->getModel(),
            ],
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
        ],
    ], $info);

    file_put_contents($reportPath, json_encode($report) . PHP_EOL, FILE_APPEND);
}


// is fixture
if ($fixtureRawPath) {
    $fixtureContent = file_get_contents($fixtureRawPath);
    $repositoryFixtures = json_decode($fixtureContent, true);

    foreach ($repositoryFixtures as $repositoryId => $item) {
        foreach ($item['files'] as $file) {
            if (empty($file)) {
                continue;
            }
            $useragents = parseFixtureFile($repositoryId, $file);
            foreach ($useragents as $useragent) {
                if (empty($useragent)) {
                    continue;
                }
                createReport($useragent, $reportName);
            }
        }
    }
}



