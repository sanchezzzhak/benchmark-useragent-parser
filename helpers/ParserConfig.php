<?php


namespace app\helpers;


class ParserConfig
{
    public const PROJECT_MATOMO_DEVICE_DETECTOR = 'matomo/device-detector';
    public const PROJECT_WHICHBROWSER_PARSER = 'whichbrowser/parser';
    public const PROJECT_MIMMI20_BROWSER_DETECTOR = 'mimmi20/browser-detector';

    public const REPOSITORIES = [
        self::PROJECT_MATOMO_DEVICE_DETECTOR => [
            'https://github.com/matomo-org/device-detector.git',
            'master',
            'id' => 1
        ],
        self::PROJECT_WHICHBROWSER_PARSER => [
            'https://github.com/WhichBrowser/Parser-PHP.git',
            'master',
            'id' => 2
        ],
        self::PROJECT_MIMMI20_BROWSER_DETECTOR => [
            'https://github.com/mimmi20/BrowserDetector.git',
            'master',
            'id' => 3
        ]
    ];

    /**
     * @param string $repositoryId
     * @return int
     */
    public static function getSourceIdByRepository(string $repositoryId ): int
    {
        return self::REPOSITORIES[$repositoryId]['id'] ?? 0;
    }

}