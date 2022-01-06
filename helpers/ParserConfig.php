<?php


namespace app\helpers;


use yii\helpers\ArrayHelper;

class ParserConfig
{
    public const PROJECT_MATOMO_DEVICE_DETECTOR = 'matomo/device-detector';
    public const PROJECT_WHICHBROWSER_PARSER = 'whichbrowser/parser';
    public const PROJECT_MIMMI20_BROWSER_DETECTOR = 'mimmi20/browser-detector';
    public const PROJECT_FOROCO_BROWSERDETECTION = 'foroco/browser-detection';

    public const REPOSITORIES = [
        self::PROJECT_MATOMO_DEVICE_DETECTOR => [
            'https://github.com/matomo-org/device-detector.git',
            'master',
            'id' => 1,
            'name' => 'matomo/device-detector'
        ],
        self::PROJECT_WHICHBROWSER_PARSER => [
            'https://github.com/WhichBrowser/Parser-PHP.git',
            'master',
            'id' => 2,
            'name' => 'whichbrowser/parser'
        ],
        self::PROJECT_MIMMI20_BROWSER_DETECTOR => [
            'https://github.com/mimmi20/BrowserDetector.git',
            'master',
            'id' => 3,
            'name' => 'mimmi20/browser-detector'
        ],
        self::PROJECT_FOROCO_BROWSERDETECTION => [
            'https://github.com/foroco/php-browser-detection.git',
            'master',
            'id' => 4,
            'name' => 'foroco/browser-detection'
        ]
    ];

    public static function getNameById(int $repositoryId) {
        $map = ArrayHelper::index(self::REPOSITORIES, 'id');
        return $map[$repositoryId]['name'] ?? '';
    }


    /**
     * @param string $repositoryId
     * @return int
     */
    public static function getSourceIdByRepository(string $repositoryId ): int
    {
        return self::REPOSITORIES[$repositoryId]['id'] ?? 0;
    }

}