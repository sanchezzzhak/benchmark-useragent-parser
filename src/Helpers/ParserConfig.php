<?php


namespace App\Helpers;


class ParserConfig
{
    public const PROJECT_MATOMO_DEVICE_DETECTOR = 'matomo/device-detector';
    public const PROJECT_WHICHBROWSER_PARSER = 'whichbrowser/parser';
    public const PROJECT_MIMMI20_BROWSER_DETECTOR = 'mimmi20/browser-detector';

    public const REPOSITORIES = [
        self::PROJECT_MATOMO_DEVICE_DETECTOR => [
            'https://github.com/matomo-org/device-detector.git',
            'master',
        ],
        self::PROJECT_WHICHBROWSER_PARSER => [
            'https://github.com/WhichBrowser/Parser-PHP.git',
            'master'
        ],
        self::PROJECT_MIMMI20_BROWSER_DETECTOR => [
            'https://github.com/mimmi20/BrowserDetector.git',
            'master'
        ]
    ];
}