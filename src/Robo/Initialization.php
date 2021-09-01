<?php

namespace App\Robo;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Robo\Tasks;
use App\Helper\ParserConfig;

class Initialization extends Tasks
{
    private string $repositoriesDir;

    public function __construct()
    {
        $this->repositoriesDir = realpath(__DIR__ . '/../GitRepository') . DIRECTORY_SEPARATOR;
    }

    /**
     * Init install all repositories
     */
    public function initRepositories()
    {
        foreach (ParserConfig::REPOSITORIES as $prefixPath => $repository) {
            [$repositoryUrl, $branch] = $repository;
            $path = $this->repositoriesDir . $prefixPath;

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
     * MatomoDeviceDetector get all paths
     * @return array
     */
    private function getFixturesMatomoDeviceParser(): array
    {
        $path = $this->repositoriesDir;
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR);
        // MatomoDeviceDetector get all paths
        $basePath = $path . ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR;

        return [
            ...glob($basePath . '/Tests/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/fixtures/*.yml'),
        ];
    }

    /**
     * WhichBrowserParser get paths
     * @return array
     */
    private function getFixturesWhichBrowserParser(): array
    {
        $path = $this->repositoriesDir;
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_WHICHBROWSER_PARSER);
        $basePath = $path . ParserConfig::PROJECT_WHICHBROWSER_PARSER;
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . 'tests/data/*', GLOB_ONLYDIR);

        $whichbrowserFixtures = [];
        foreach ($dirs as $dir) {
            $whichbrowserFixtures = array_merge($whichbrowserFixtures, [
                ...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)
            ]);
        }

        return $whichbrowserFixtures;
    }

    /**
     * Mimmi20BrowserDetector get paths
     * @return array
     */
    private function getFixturesMimmi20BrowserDetectorParser(): array
    {
        $path = $this->repositoriesDir;
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR);
        $basePath = $path . ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR;

        $ridi = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath . DIRECTORY_SEPARATOR . 'tests/data'));
        $mimmi20Fixtures = [];
        foreach ($ridi as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (pathinfo($file->getPathname(), PATHINFO_EXTENSION) !== 'json') {
                continue;
            }
            $mimmi20Fixtures[] = $file->getPathname();
        }
        return $mimmi20Fixtures;
    }

    /**
     * Init paths fixtures
     */
    public function initFixtures()
    {
        $matomoFixtures = $this->getFixturesMatomoDeviceParser();
        $whichbrowserFixtures = $this->getFixturesWhichBrowserParser();
        $mimmi20Fixtures = $this->getFixturesMimmi20BrowserDetectorParser();

        // save paths in file
        $json = [
            ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR => [
                'files' => [...$matomoFixtures]
            ],
            ParserConfig::PROJECT_WHICHBROWSER_PARSER => [
                'files' => [...$whichbrowserFixtures]
            ],
            ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR => [
                'files' => [...$mimmi20Fixtures]
            ],
        ];

        file_put_contents(__DIR__ . '/../../data/paths.json', json_encode($json));
    }

}