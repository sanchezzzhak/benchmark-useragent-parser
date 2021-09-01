<?php
require_once __DIR__ . '/../../Helpers/Benchmark.php';
require_once __DIR__ . '/../../GitRepository/whichbrowser/parser/vendor/autoload.php';
require_once __DIR__ . '/../../functions.php';

use App\Helper\Benchmark;
use WhichBrowser\Parser;


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
    $parser = null;
    $useragentHeader = 'UserAgent: ' . $useragent;
    $info = Benchmark::benchmarkWithCallback(function () use (&$parser, $useragentHeader) {
        $parser = new Parser($useragentHeader, ['detectBots' => true]);
    });

    return array_merge([
        'user_agent' => $useragent,
        'result' => [
            'browser' => $parser->browser->toArray(),
            'engine' => $parser->engine->toArray(),
            'os' => $parser->os->toArray(),
            'device' => $parser->device->toArray()
        ]
    ], $info);
}

if ($fixtureRawPath !== null) {
    runTestsFixture($fixtureRawPath, $reportName);
}
