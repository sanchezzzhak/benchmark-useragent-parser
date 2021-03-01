<?php

namespace App\Parser;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

/**
 * Class MatomoDeviceDetector
 * @package App\Parser
 */
class MatomoDeviceDetector extends AbstractParser
{
    private DeviceDetector $parser;

    public function __construct()
    {
        $this->parser = new DeviceDetector;
    }

    public function parseUserAgent(string $useragent): array
    {
        $start = microtime(true);
        $this->parser->setUserAgent($useragent);
        $this->parser->parse();
        $time = microtime(true) - $start;

        if ($this->parser->isBot()) {
            return [
                'user_agent' => $useragent,
                'bot' => $this->parser->getBot(),
                'time' => $time,
            ];
        }

        $osFamily = OperatingSystem::getOsFamily($this->parser->getOsAttribute('name')) ?? '';
        $browserFamily = Browser::getBrowserFamily($this->parser->getClientAttribute('name')) ?? '';



        return [
            'user_agent' => $useragent,
            'os' => $this->parser->getOs(),
            'client' => $this->parser->getClient(),
            'device' => [
                'type' => $this->parser->getDeviceName(),
                'brand' => $this->parser->getBrandName(),
                'model' => $this->parser->getModel(),
            ],
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
            'time' => $time,
        ];
    }

    public static function getFixtureUseragent($data): string
    {
        return $data['user_agent'] ?? '';
    }

    public static function getFixtures(): array
    {
        $basePath = __DIR__ . '/../../vendor/matomo/device-detector/Tests';
        return [
            ...glob($basePath . '/fixtures/*.yml'),
            ...glob($basePath . '/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Parser/fixtures/*.yml'),
        ];
    }
}