<?php

require_once __DIR__ . '/../../functions.php';
require_once dirname(__DIR__) . '/../../vendor/autoload_runtime.php';
require_once __DIR__ . '/../../GitRepository/whichbrowser/parser/bootstrap.php';

use App\Entity\BenchmarkResult;
use App\Entity\DeviceDetectorResult;
use App\Helper\ParserConfig;
use App\Kernel;
use App\Helper\Benchmark;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use WhichBrowser\Parser;

return function (array $context) {

    $kernel = new Kernel($context['APP_ENV'] ?? 'dev', (bool)$context['APP_DEBUG']);
    $command = new Command('process');
    $command->setCode(function (InputInterface $input, OutputInterface $output) use ($kernel) {

        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repoTests = $em->getRepository(BenchmarkResult::class);
        $repoResult = $em->getRepository(DeviceDetectorResult::class);

        $findOrCreateDeviceResult = fn ($id) => $repoResult->findOneBy(['bench_id' => $id]) ?? new DeviceDetectorResult;
        $query = $repoTests->createQueryBuilder('br')->getQuery();
        /** @var Parser $parser */
        $parser = null;

        // batch read
        /** @var BenchmarkResult $row */
        foreach ($query->toIterable() as $row) {
            $useragent = $row->getUserAgent();

            $useragentHeader = 'UserAgent: ' . $useragent;
            $info = Benchmark::benchmarkWithCallback(function () use (&$parser, $useragentHeader) {
                $parser = new Parser($useragentHeader, ['detectBots' => true]);
            });

            $section = $output->section();
            $section->overwrite(sprintf('#%s parse %s', $row->getId(), $row->getUserAgent()));

            $detectResult = [
                'browser' => $parser->browser->toArray(),
                'engine' => $parser->engine->toArray(),
                'os' => $parser->os->toArray(),
                'device' => $parser->device->toArray()
            ];
            $deviceType = $detectResult['device']['type'] ?? '';


            $model = $findOrCreateDeviceResult($row->getId());
            if ($model->getBenchId() === null) {
                $model->setBenchId($row->getId());
                $model->setScore(0);
                $model->setParserId(
                    ParserConfig::getSourceIdByRepository(
                        ParserConfig::PROJECT_WHICHBROWSER_PARSER
                    )
                );
            }

            try {
                $model->setTime($info['time']);
                $model->setMemory($info['memory']);
                $model->setDataJson(json_encode($detectResult));

                if ($deviceType === 'bot') {
                    $model->setIsBot(true);
                    $model->setBotName($detectResult['browser']['name'] ?? '');
                    $model->setDeviceType($deviceType);
                    $em->persist($model);
                    $em->flush();
                    continue;
                }

                $model->setClientType($detectResult['browser']['type'] ?? '');

                if (isset($detectResult['browser']['version']) && is_array($detectResult['browser']['version'])) {
                    $model->setClientVersion($detectResult['browser']['version']['value'] ?? '');
                } else {
                    $model->setClientVersion($detectResult['browser']['version'] ?? '');
                }

                $model->setClientName($detectResult['browser']['name'] ?? '');

                $model->setEngineName($detectResult['engine']['name'] ?? '');
                $model->setEngineVersion($detectResult['engine']['version'] ?? '');

                $model->setDeviceType($deviceType);
                $model->setBrandName($detectResult['device']['manufacturer'] ?? '');
                $model->setModelName($detectResult['device']['model'] ?? '');

                $em->persist($model);
                $em->flush();
            } catch (\Throwable $exception) {
                dd($detectResult, $exception);

            }
        }
    });
    $app = new Application($kernel);
    $app->add($command);
    $app->setDefaultCommand('process', true);
    return $app;
};

