<?php


namespace App\Robo;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Robo\Tasks;
use App\Helper\ParserConfig;

class Initialization extends Tasks
{

    /**
     * Init install all repositories
     */
    public function initRepositories()
    {
        foreach (ParserConfig::REPOSITORIES as $prefixPath => $repository) {
            [$repositoryUrl, $branch] = $repository;
            $path = realpath(__DIR__ . '/../GitRepository') . DIRECTORY_SEPARATOR . $prefixPath;

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
        $path = realpath(__DIR__ . '/../GitRepository');
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR);
        // MatomoDeviceDetector get all paths
        $basePath = $path . DIRECTORY_SEPARATOR . ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR;

        $matomoFixtures = [
            ...glob($basePath . '/Tests/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Tests/Parser/fixtures/*.yml'),
        ];

        // WhichBrowserParser get all paths
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_WHICHBROWSER_PARSER);
        $basePath = $path . DIRECTORY_SEPARATOR . ParserConfig::PROJECT_WHICHBROWSER_PARSER;

        $dirs = glob($basePath . DIRECTORY_SEPARATOR . 'tests/data/*', GLOB_ONLYDIR);
        $whichbrowserFixtures = [];
        foreach ($dirs as $dir) {
            $whichbrowserFixtures = array_merge($whichbrowserFixtures, [...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)]);
        }

        // Mimmi20BrowserDetector get all paths
        $this->say('get fixtures paths in ' . ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR);
        $basePath = $path . DIRECTORY_SEPARATOR . ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR;

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

        // save paths in file;
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

    /**
     * Run all parsers
     */
    public function initStat()
    {
        $path = __DIR__ . '/../Parser/';
//        $this->taskExec('php ' . $path . 'MotamoDeviceDetector.php')->run();
    }

}