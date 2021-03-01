<?php


namespace App\Robo;

use Robo\Tasks;

class RoboFile extends Tasks
{

    private const PROJECT_MATOMO_DEVICE_DETECTOR = 'matomo/device-detector';
    private const PROJECT_WHICHBROWSER_PARSER = 'whichbrowser/parser';


    private const REPOSITORIES = [
        [
            self::PROJECT_MATOMO_DEVICE_DETECTOR,
            'https://github.com/matomo-org/device-detector.git',
            'master'
        ], [
            self::PROJECT_WHICHBROWSER_PARSER,
            'https://github.com/WhichBrowser/Parser-PHP.git',
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

            $this->taskGitStack()
                ->cloneRepo($repositoryUrl, $path, $branch)
                ->pull()
                ->run();

            $this->taskComposerUpdate()
                ->workingDir($path)
                ->run();
        }
    }

    /**
     * Init paths fixtures
     */
    public function initFixtures()
    {
        $path = __DIR__ . '/../Repository/';
        // Matomo get all paths
        $basePath = $path . self::PROJECT_MATOMO_DEVICE_DETECTOR;
        $matomoFixtures = [
            ...glob($basePath . '/fixtures/*.yml'),
            ...glob($basePath . '/Parser/Client/fixtures/*.yml'),
            ...glob($basePath . '/Parser/Device/fixtures/*.yml'),
            ...glob($basePath . '/Parser/fixtures/*.yml'),
        ];

        // WhichBrowser get all paths
        $basePath = $path . self::PROJECT_WHICHBROWSER_PARSER;
        $dirs = glob($basePath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        $whichbrowserFixtures = [];
        foreach ($dirs as $dir) {
            $whichbrowserFixtures = array_merge($whichbrowserFixtures, [...glob($dir . DIRECTORY_SEPARATOR . '*.{yaml,yml}', GLOB_BRACE)]);
        }

        $json=  [
            self::PROJECT_MATOMO_DEVICE_DETECTOR => [
                'files' => [...$matomoFixtures]
            ],
            self::PROJECT_WHICHBROWSER_PARSER => [
                'files' => [...$whichbrowserFixtures]
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
        $this->taskExec('php '.  $path  . 'MotamoDeviceDetector.php')->run();
    }

}