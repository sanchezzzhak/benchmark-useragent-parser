<?php


namespace App\Parser;

use WhichBrowser\Parser;

/**
 * Class WhichBrowserParserPHP
 * @package App\Parser
 */
class WhichBrowserParserPHP extends AbstractParser
{

    public function parseUserAgent(string $useragent): array
    {
        $parser = new Parser($useragent);

        return [
            'useragent' => $useragent,
            'browser' => [
                'name' => !empty($parser->browser->name) ? $parser->browser->name : null,
                'version' => !empty($parser->browser->version) ? $parser->browser->version->value : null,
            ],
            'platform' => [
                'name' => !empty($parser->os->name) ? $parser->os->name : null,
                'version' => !empty($parser->os->version->value) ? $parser->os->version->value : null,
            ],
            'device' => [
                'name' => !empty($parser->device->model) ? $parser->device->model : null,
                'brand' => !empty($parser->device->manufacturer) ? $parser->device->manufacturer : null,
                'type' => !empty($parser->device->type) ? $parser->device->type : null,
                'ismobile' => $parser->isMobile() ? true : false,
            ],
//            'time' => $end,
        ];
    }

    public static function getFixtureUseragent($data): string
    {
        $raw = $data['headers'] ?? '';
        if ($raw === '') {
            return '';
        }
        return explode('User-Agent: ', $raw)[1] ?? '';
    }

    public function getFixtures(): array
    {
        $basePath = realpath(__DIR__ . '/../../vendor/whichbrowser/parser/tests/data');
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        $result = [];
        foreach ($dirs as $dir) {
            $result = array_merge($result, [...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)]);
        }
        return $result;
    }

}