<?php



require_once __DIR__ . '/../../functions.php';
require_once dirname(__DIR__).'/../../vendor/autoload_runtime.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Entity\BenchmarkResult;
use App\Kernel;
use App\Helper\Benchmark;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

return function (array $context) {



    $kernel = new Kernel($context['APP_ENV'] ?? 'dev', (bool) $context['APP_DEBUG']);
    $command = new Command('process');
    $command->setCode(function (InputInterface $input, OutputInterface $output) use($kernel) {

        static $parser;
        if (!$parser) {
            $parser = new DeviceDetector();
        }

        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(BenchmarkResult::class);
        $query = $repo->createQueryBuilder('br')->getQuery();
        // batch read
        foreach($query->toIterable() as $row) {
            dd($row);
        }

    });


    $app = new Application($kernel);
    $app->add($command);
    $app->setDefaultCommand('process', true);
    return $app;
};

/*
$options = getopt(null, [
    "fixtures::", // use paths fixtures useragents
    "files::",    // use files useragents
    "report::",   // set custom report name
    "date::"      // set date mark report
]);
$fixtureRawPath = $options['fixtures'] ?? null;
$fileRawPath = $options['files'] ?? null;
$reportName = $options['report'] ?? 'default';

if ($fileRawPath === null && $fixtureRawPath === null) {
    throw new InvalidArgumentException('args: fixtures or files not required');
}

function createReport(string $useragent)
{


    $info = Benchmark::benchmarkWithCallback(function () use ($parser, $useragent) {
        $parser->setUserAgent($useragent);
        $parser->parse();
    });

    if ($parser->isBot()) {
        return array_merge([
            'user_agent' => $useragent,
            'result' => [
                'bot' => $parser->getBot(),
            ],
        ], $info);
    }

    $osFamily = OperatingSystem::getOsFamily($parser->getOs('name')) ?? '';
    $browserFamily = Browser::getBrowserFamily($parser->getClient('name')) ?? '';

    $osData = $parser->getOs();
    $clientData = $parser->getClient();
    unset($osData['short_name']);
    unset($clientData['short_name']);

    return array_merge([
        'user_agent' => $useragent,
        'result' => [
            'os' => $osData,
            'client' => $clientData,
            'device' => [
                'type' => $parser->getDeviceName(),
                'brand' => $parser->getBrandName(),
                'model' => $parser->getModel(),
            ],
            'os_family' => $osFamily,
            'browser_family' => $browserFamily,
        ],
    ], $info);
}

if ($fixtureRawPath !== null) {
    runTestsFixture($fixtureRawPath, $reportName);
}

*/