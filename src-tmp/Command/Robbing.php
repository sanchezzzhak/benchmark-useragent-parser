<?php

namespace App\Command;

use App\Entity\BenchmarkResult;
use App\Helper\ParserConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

class Robbing extends Command
{
    protected static $defaultName = 'robbing:useragents';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(
            'checkExist',
            InputArgument::REQUIRED,
            'Check insert [yes/no]?'
        );
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


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceFile = dirname(__DIR__, 2) . '/data/paths.json';

        $entityManager = $this->entityManager;
        $benchmarkRepository = $entityManager->getRepository(BenchmarkResult::class);

        $output->writeln(sprintf('<info>parse paths for file: %s</info>', $sourceFile));

        $checkExist = strtolower($input->getArgument('checkExist')) === 'yes';



    }

}