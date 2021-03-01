<?php


namespace App\Parser;
use BrowserDetector\Detector;
use BrowserDetector\DetectorFactory;

class Mimmi20BrowserDetector extends AbstractParser
{

    private Detector $parser;

    public function __construct()
    {
        $factory = new DetectorFactory;
        $this->parser = $factory();
    }


    public function parseUserAgent(string $useragent): array
    {
        $start = microtime(true);
        $result = $this->parser($useragent);
        $time = microtime(true) - $start;

        return [
            'user_agent' => $useragent,
            'time' => $time
        ];
    }

    public static function getFixtures(): array
    {
        $basePath = realpath(__DIR__ . '/../../vendor/mimmi20/browser-detector/tests/data');
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        $result = [];
        foreach ($dirs as $dir) {
            $result = array_merge($result, [...glob($dir . DIRECTORY_SEPARATOR . '*.{json}', GLOB_BRACE)]);
        }
        return $result;
    }

    public static function getFixtureUseragent($data): string
    {
        return $data['headers']['user-agent'] ?? '';
    }
}