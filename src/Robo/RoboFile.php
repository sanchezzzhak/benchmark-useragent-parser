<?php


namespace App\Robo;

use Robo\Tasks;

class RoboFile extends Tasks
{

    private const PROJECT_MATOMO_DEVICE_DETECTOR = 'matomo/device-detector';
    private const PROJECT_WHICHBROWSER_PARSER = 'whichbrowser/parser';
    private const PROJECT_MIMMI20_BROWSER_DETECTOR = 'mimmi20/browser-detector';


    private const REPOSITORIES = [
        [
            self::PROJECT_MATOMO_DEVICE_DETECTOR,
            'https://github.com/matomo-org/device-detector.git',
            'master'
        ], [
            self::PROJECT_WHICHBROWSER_PARSER,
            'https://github.com/WhichBrowser/Parser-PHP.git',
            'master'
        ], [
            self::PROJECT_MIMMI20_BROWSER_DETECTOR,
            'https://github.com/mimmi20/BrowserDetector.git',
            'master'
        ]
    ];

    /**
     * Init install all repositories parsers
     */
    public function initRepositories()
    {
        foreach (self::REPOSITORIES as $repository) {
            [$prefixPath, $repositoryUrl, $branch] = $repository;
            $path = realpath(__DIR__ . '/../Repository/' . $prefixPath);

            if (!is_dir($path)) {
                $this->taskGitStack()
                    ->cloneRepo($repositoryUrl, $path, $branch)
                    ->run();
            } else {
                $this->taskGitStack()
                    ->dir($path)
                    ->pull()
                    ->run();
            }

            $this->taskComposerUpdate()
                ->dir($path)
                ->run();
        }
    }


    /**
     * Init paths fixtures
     */
    public function initFixtures()
    {
        $path = realpath(__DIR__ . '/../Repository/');
        $this->say('get fixtures paths in ' . self::PROJECT_MATOMO_DEVICE_DETECTOR);
        // MatomoDeviceDetector get all paths
        $basePath = $path . self::PROJECT_MATOMO_DEVICE_DETECTOR;
        $matomoFixtures = [
            ...glob($basePath . '/Tests/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/fixtures/*.yml'),
        ];

        // WhichBrowserParser get all paths
        $this->say('get fixtures paths in ' . self::PROJECT_WHICHBROWSER_PARSER);
        $basePath = $path . self::PROJECT_WHICHBROWSER_PARSER;
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . 'tests/*', GLOB_ONLYDIR);
        $whichbrowserFixtures = [];
        foreach ($dirs as $dir) {
            $whichbrowserFixtures = array_merge($whichbrowserFixtures, [...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)]);
        }

        // Mimmi20BrowserDetector get all paths
        $this->say('get fixtures paths in ' . self::PROJECT_MIMMI20_BROWSER_DETECTOR);
        $basePath = $path . self::PROJECT_MIMMI20_BROWSER_DETECTOR;
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . 'tests/data/*', GLOB_ONLYDIR);
        $mimmi20Fixtures = [];
        foreach ($dirs as $dir) {
            $mimmi20Fixtures = array_merge($mimmi20Fixtures, [...glob($dir . DIRECTORY_SEPARATOR . '*.{json}', GLOB_BRACE)]);
        }

        // save paths in file;
        $json = [
            self::PROJECT_MATOMO_DEVICE_DETECTOR => [
                'files' => [...$matomoFixtures]
            ],
            self::PROJECT_WHICHBROWSER_PARSER => [
                'files' => [...$whichbrowserFixtures]
            ],
            self::PROJECT_MIMMI20_BROWSER_DETECTOR => [
                'files' => [...$mimmi20Fixtures]
            ],
        ];

        file_put_contents(__DIR__ . '/../../data/paths.json', json_encode($json));
    }

    /**
     * Run all parsers
     */
    public function initStat()
    {
        $path = __DIR__ . '/../Parser/';
//        $this->taskExec('php ' . $path . 'MotamoDeviceDetector.php')->run();
    }

}