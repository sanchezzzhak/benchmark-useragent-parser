<?php
use Symfony\Component\Yaml\Yaml;

webNotSupportedRunning();

function webNotSupportedRunning(): void
{
    if ('cli' !== php_sapi_name()) {
        echo 'web not supported running' . PHP_EOL;
        exit;
    }
}

function parseFixtureFile(string $repositoryId, string $file): array
{
    try {
        if ($repositoryId === 'matomo/device-detector') {
            $data = Yaml::parseFile($file);
            if (is_array($data)) {
                return array_map(fn($item) => $item['user_agent'] ?? '', $data);
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
                return array_map(fn($item) => $item['headers']['user-agent'] ?? '', $data);
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

function rubTestFile($file, $reportName)
{
    $fw = fopen($reportName, "w");
    $fr = fopen($file, 'r');
    while (!feof($fr)) {
        $useragent = fgets($fr);

        if (false === $useragent) {
            break;
        }

        $useragent = trim($useragent);

        if (empty($useragent)) {
            continue;
        }

        $report = createReport($useragent);
        fwrite($fw, json_encode($report) . PHP_EOL);
    }
    fclose($fr);
    fclose($fw);
}

function runTestsFixture($fixtureRawPath, $reportName)
{
    $fh = fopen($reportName, "w");
    if ($fixtureRawPath) {
        $fixtureContent = file_get_contents($fixtureRawPath);
        $repositoryFixtures = json_decode($fixtureContent, true);
        foreach ($repositoryFixtures as $repositoryId => $item) {
            echo "parse {$repositoryId}\n";
            foreach ($item['files'] as $file) {
                if (empty($file)) {
                    continue;
                }
                echo "parse file {$file}\n";
                $useragents = parseFixtureFile($repositoryId, $file);
                foreach ($useragents as $useragent) {
                    if (empty($useragent)) {
                        continue;
                    }
                    $report = createReport($useragent);
                    fwrite($fh, json_encode($report) . PHP_EOL);
                }
            }
        }
    }
    fclose($fh);
}
