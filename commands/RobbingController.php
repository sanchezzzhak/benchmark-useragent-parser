<?php

namespace app\commands;

use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use yii\console\Controller;
use yii\console\ExitCode;
use Symfony\Component\Yaml\Yaml;
use yii\helpers\Console;

/**
 * Class HelloController
 * @package app\commands
 */
class RobbingController extends Controller
{

    private $repositoriesDir;

    public function init()
    {
        parent::init();
        $this->repositoriesDir = \Yii::getAlias('@vendor/');
    }

    /**
     * MatomoDeviceDetector get all paths
     * @return array
     */
    private function getFixturesMatomoDeviceParser(): array
    {
        $path = $this->repositoriesDir;
        $this->stdout('get fixtures paths in ' . ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR . PHP_EOL);
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
        $this->stdout('get fixtures paths in ' . ParserConfig::PROJECT_WHICHBROWSER_PARSER . PHP_EOL);
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
        $this->stdout('get fixtures paths in ' . ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR . PHP_EOL);
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

    private function parseFixtureFile(string $repositoryId, string $file): array
    {
        try {
            if ($repositoryId === 'matomo/device-detector') {
                $data = Yaml::parseFile($file);
                if (is_array($data)) {
                    return array_map(fn ($item) => $item['user_agent'] ?? '', $data);
                }
            }
            if ($repositoryId === 'whichbrowser/parser') {
                $data = Yaml::parseFile($file);
                if (is_array($data)) {
                    return array_map(function ($item) {
                        $useragent = '';
                        if (is_string($item['headers']) && preg_match('~User-Agent: (.*)$~i', $item['headers'], $match)) {
                            $useragent = $match[0] ?? '';
                        } else if (is_array($item['headers'])) {
                            $useragent = $item['headers']['User-Agent'] ?? '';
                        }
                        return $useragent;
                    }, $data);
                }
            }

            if ($repositoryId === 'mimmi20/browser-detector') {
                $data = json_decode(file_get_contents($file), true);
                if (is_array($data)) {
                    return array_map(fn ($item) => $item['headers']['user-agent'] ?? '', $data);
                }
            }

        } catch (Exception $exception) {
            $message = sprintf(
                "Error: %s\nFile Parse: %s\nRepositoryId: %s",
                $exception->getMessage(),
                $file,
                $repositoryId
            );
            throw new Exception($message, 0, $exception);
        }

        return [];
    }

    public function actionIndex()
    {
        $checkExist = $this->confirm('Check the useragent for a duplicate?');

        $messages = array_map(function($item) {
            return "id {$item['id']} repository {$item[0]}\n";
        }, ParserConfig::REPOSITORIES);
        $message = sprintf("Specify id separated by separator `,` which sets to skip,\n%s", implode('', $messages));
        $skipParsers = Console::input($message);
        $skipParsers = explode(',', $skipParsers);

        $matomoFixtures = $this->getFixturesMatomoDeviceParser();
        $whichbrowserFixtures = $this->getFixturesWhichBrowserParser();
        $mimmi20Fixtures = $this->getFixturesMimmi20BrowserDetectorParser();

        $repositoryFixtures = [
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
        foreach ($repositoryFixtures as $repositoryId => $item) {
            if (in_array((string)$repositoryId, $skipParsers)) {
                continue;
            }
            $sourceParserId = ParserConfig::getSourceIdByRepository($repositoryId);

            $this->stdout(sprintf('-> <info>grab repository: %s</info>' . PHP_EOL, $repositoryId));
            foreach ($item['files'] as $file) {
                if (empty($file)) {
                    continue;
                }
                $useragents = $this->parseFixtureFile($repositoryId, $file);

                $this->stdout(sprintf('--> <info>:🗃 file: %s</info>', $file) . PHP_EOL);
//                $progressBar = new ProgressBar($output, count($useragents));
                foreach ($useragents as $useragent) {
//                    $progressBar->advance();
                    if (empty($useragent)) {
                        continue;
                    }
                    $benchmarkResult = null;
                    if ($checkExist) {
                        $benchmarkResult = BenchmarkResult::findOne([
                            'user_agent' => $useragent,
                            'source_id' => $sourceParserId
                        ]);
                    }
                    // save
                    if ($benchmarkResult === null) {
                        $benchmarkResult = new BenchmarkResult([
                            'user_agent' => $useragent,
                            'source_id' => $sourceParserId
                        ]);
                        $benchmarkResult->save();
                    }
                }

            }
        }

        return ExitCode::OK;
    }

}
