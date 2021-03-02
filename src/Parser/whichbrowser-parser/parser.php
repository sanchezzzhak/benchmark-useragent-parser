<?php
require_once __DIR__ . '/../../Helpers/Benchmark.php';
require_once __DIR__ . '/../../Repository/whichbrowser/parser/vendor/autoload.php';
require_once __DIR__ . '/../../functions.php';

use App\Helpers\Benchmark;
use WhichBrowser\Parser;


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
    $parser = null;
    $useragentHeader = 'UserAgent: ' . $useragent;
    $info = Benchmark::benchmarkWithCallback(function () use (&$parser, $useragentHeader) {
        $parser = new Parser($useragentHeader);
    });

    $report = array_merge([
        'user_agent' => $useragent,
        'client' => [
            'name' => !empty($parser->browser->name) ? $parser->browser->name : null,
            'version' => !empty($parser->browser->version) ? $parser->browser->version->value : null,
        ],
        'os' => [
            'name' => !empty($parser->os->name) ? $parser->os->name : null,
            'version' => !empty($parser->os->version->value) ? $parser->os->version->value : null,
        ],
        'device' => [
            'name' => !empty($parser->device->model) ? $parser->device->model : null,
            'brand' => !empty($parser->device->manufacturer) ? $parser->device->manufacturer : null,
            'type' => !empty($parser->device->type) ? $parser->device->type : null,
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