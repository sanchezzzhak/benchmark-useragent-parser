<?php

require_once __DIR__ . '/../../Helpers/Benchmark.php';
require_once __DIR__ . '/../../GitRepository/mimmi20/browser-detector/vendor/autoload.php';
require_once __DIR__ . '/CacheFake.php';
require_once __DIR__ . '/LoggerFake.php';
require_once __DIR__ . '/../../functions.php';

use App\Helpers\Benchmark;
use BrowserDetector\DetectorFactory;
use UaResult\Result\Result;

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

function createReport(string $useragent): array
{
    static $parser;
    if (!$parser) {
        $logger = new LoggerFake;
        $cache = new CacheFake;
        $factory = new DetectorFactory($cache, $logger);
        $parser = $factory();
    }
    /** @var Result|null $result */
    $result = null;
    $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent, &$result) {
        $result = $parser($useragent);
    });

    return array_merge([
        'user_agent' => $useragent,
        'result' => [
            'os' => $result->getOs()->toArray(),
            'client' => $result->getBrowser()->toArray(),
            'device' => $result->getDevice()->toArray(),
            'engine' => $result->getEngine()
        ],
    ], $info);

}

if ($fixtureRawPath !== null) {
    runTestsFixture($fixtureRawPath, $reportName);
}
