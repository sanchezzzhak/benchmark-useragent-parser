<?php


namespace App\Robo;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Robo\Tasks;

class Initialization extends Tasks
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
     * Init install all repositories
     */
    public function initRepositories()
    {
        foreach (self::REPOSITORIES as $repository) {
            [$prefixPath, $repositoryUrl, $branch] = $repository;
            $path = realpath(__DIR__ . '/../Repository') . DIRECTORY_SEPARATOR . $prefixPath;

            $this->say("init repository {$repositoryUrl} into {$path}");

            $hasInstall = !is_dir($path);
            $hasInstall
                ? $this->taskGitStack()->cloneRepo($repositoryUrl, $path, $branch)->run()
                : $this->taskGitStack()->dir($path)->pull()->run();;

            $hasComposer = is_file($path . '/composer.json');
            $this->say("check composer is exist: " . ($hasComposer ? 'true' : 'false'));
            if ($hasComposer) {
                $this->taskComposerUpdate()
                    ->dir($path)
                    ->run();
                continue;
            }
        }
    }

    /**
     * Init paths fixtures
     */
    public function initFixtures()
    {
        $path = realpath(__DIR__ . '/../Repository');
        $this->say('get fixtures paths in ' . self::PROJECT_MATOMO_DEVICE_DETECTOR);
        // MatomoDeviceDetector get all paths
        $basePath = $path . DIRECTORY_SEPARATOR . self::PROJECT_MATOMO_DEVICE_DETECTOR;

        $matomoFixtures = [
            ...glob($basePath . '/Tests/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/fixtures/*.yml'),
        ];

        // WhichBrowserParser get all paths
        $this->say('get fixtures paths in ' . self::PROJECT_WHICHBROWSER_PARSER);
        $basePath = $path . DIRECTORY_SEPARATOR . self::PROJECT_WHICHBROWSER_PARSER;

        $dirs = glob($basePath . DIRECTORY_SEPARATOR . 'tests/data/*', GLOB_ONLYDIR);
        $whichbrowserFixtures = [];
        foreach ($dirs as $dir) {
            $whichbrowserFixtures = array_merge($whichbrowserFixtures, [...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)]);
        }

        // Mimmi20BrowserDetector get all paths
        $this->say('get fixtures paths in ' . self::PROJECT_MIMMI20_BROWSER_DETECTOR);
        $basePath = $path . DIRECTORY_SEPARATOR . self::PROJECT_MIMMI20_BROWSER_DETECTOR;

        $ridi = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath . DIRECTORY_SEPARATOR . 'tests/data'));
        $mimmi20Fixtures = [];
        foreach ($ridi as $file) {
            if ($file->isDir()){
                continue;
            }
            $mimmi20Fixtures[] = $file->getPathname();
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