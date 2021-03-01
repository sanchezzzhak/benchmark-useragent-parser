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
        $start = microtime(true);
        $parser = new Parser($useragent);
        $time = microtime(true) - $start;

        return [
            'user_agent' => $useragent,
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
           'time' => $time,
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

    }

}