<?php

require_once dirname(__DIR__) . '/../../vendor/autoload_runtime.php';
require_once __DIR__ . '/../../GitRepository/matomo/device-detector/autoload.php';
require_once __DIR__ . '/../../GitRepository/matomo/device-detector/vendor/mustangostang/spyc/Spyc.php';

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

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

return function (array $context) {

    AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

    $kernel = new Kernel($context['APP_ENV'] ?? 'dev', (bool)$context['APP_DEBUG']);
    $command = new Command('process');
    $command->setCode(function (InputInterface $input, OutputInterface $output) use ($kernel) {

        $parser = new DeviceDetector();
        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repoTests = $em->getRepository(BenchmarkResult::class);
        $repoResult = $em->getRepository(DeviceDetectorResult::class);

        $parserId = ParserConfig::getSourceIdByRepository(
            ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR
        );

        $findOrCreateDeviceResult = fn ($id) =>
            $repoResult->findOneBy([
                'bench_id' => $id, 'parser_id' => $parserId
            ]) ?? new DeviceDetectorResult;
        $query = $repoTests->createQueryBuilder('br')->getQuery();

        // batch read
        /** @var BenchmarkResult $row */
        foreach ($query->toIterable() as $row) {
            $useragent = $row->getUserAgent();
            $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent) {
                $parser->setUserAgent($useragent);
                $parser->parse();
            });

            $section = $output->section();
            $section->overwrite(sprintf('#%s parse %s', $row->getId(), $row->getUserAgent()));

            $model = $findOrCreateDeviceResult($row->getId());
            if ($model->getBenchId() === null) {
                $model->setBenchId($row->getId());
                $model->setScore(0);
                $model->setParserId($parserId);
            }

            $model->setTime($info['time']);
            $model->setMemory($info['memory']);

            if ($parser->isBot()) {
                $detectResult = $parser->getBot();
                if (!is_array($detectResult)) {
                    continue;
                }
                $model->setBotName($detectResult['name'] ?? '');
                $model->setIsBot(true);
                $model->setDataJson(json_encode($detectResult));
                $em->persist($model);
                $em->flush();
                continue;
            }

            $osFamily = OperatingSystem::getOsFamily($parser->getOs('name')) ?? '';
            $browserFamily = Browser::getBrowserFamily($parser->getClient('name')) ?? '';

            $osData = $parser->getOs();
            $clientData = $parser->getClient();
            unset($osData['short_name']);
            unset($clientData['short_name']);

            $detectResult = [
                'os' => $osData,
                'client' => $clientData,
                'device' => [
                    'type' => $parser->getDeviceName(),
                    'brand' => $parser->getBrandName(),
                    'model' => $parser->getModel(),
                ],
                'os_family' => $osFamily,
                'browser_family' => $browserFamily,
            ];
            $model->setOsName($osData['name'] ?? '');
            $model->setOsVersion($osData['version'] ?? '');


            if(isset($clientData['engine'])) {
                $model->setEngineVersion($clientData['engine_version']);
                $model->setEngineName($clientData['engine']);
            }

            $model->setClientName($clientData['name'] ?? '');
            $model->setClientVersion($clientData['version'] ?? '');
            $model->setClientType($clientData['type'] ?? '');

            $model->setDeviceType($detectResult['device']['type']);
            $model->setBrandName($detectResult['device']['brand']);
            $model->setModelName($detectResult['device']['model']);

            $model->setDataJson(json_encode($detectResult));

            $em->persist($model);
            $em->flush();
        }

    });

    $app = new Application($kernel);
    $app->add($command);
    $app->setDefaultCommand('process', true);
    return $app;
};
