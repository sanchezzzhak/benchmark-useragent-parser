<?php

require_once __DIR__ . '/../../Helpers/Benchmark.php';
require_once __DIR__ . '/../../Repository/matomo/device-detector/vendor/autoload.php';
require_once __DIR__ . '/../../functions.php';

use App\Helpers\Benchmark;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

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

function createReport(string $useragent)
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
        return array_merge([
            'user_agent' => $useragent,
            'result' => [
                'bot' => $parser->getBot(),
            ],
        ], $info);
    }

    $osFamily = OperatingSystem::getOsFamily($parser->getOs('name')) ?? '';
    $browserFamily = Browser::getBrowserFamily($parser->getClient('name')) ?? '';

    $osData = $parser->getOs();
    $clientData = $parser->getClient();
    unset($osData['short_name']);
    unset($clientData['short_name']);

    return array_merge([
        'user_agent' => $useragent,
        'result' => [
            'os' => $osData,
            'client' => $clientData,
            'device' => [
                'type' => $parser->getDeviceName(),
                'brand' => $parser->getBrandName(),
                'model' => $parser->getModel(),
            ],
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
        ],
    ], $info);
}

if ($fixtureRawPath !== null) {
    runTestsFixture($fixtureRawPath, $reportName);
}

