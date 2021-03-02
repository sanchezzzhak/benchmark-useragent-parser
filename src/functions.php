<?php


$libPath = __DIR__ . '/Repository/matomo/device-detector/vendor/mustangostang/spyc/Spyc.php';
if (!function_exists('spyc_load') && is_file($libPath)) {
    require_once $libPath;
}

function webNotSupportedRunning(): void
{
    if ('cli' !== php_sapi_name()) {
        echo 'web not supported running' . PHP_EOL;
        exit;
    }
}


function parseFixtureFile(string $repositoryId, string $file): array
{
    if ($repositoryId === 'matomo/device-detector') {
        $data = spyc_load_file($file);
        if (is_array($data)) {
            return array_map(fn($item) => $item['user_agent'] ?? '', $data);
        }
    }

    if ($repositoryId === 'whichbrowser/parser') {
        $data = spyc_load_file($file);
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

    return [];
}

