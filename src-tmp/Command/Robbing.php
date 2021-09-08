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




    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceFile = dirname(__DIR__, 2) . '/data/paths.json';

        $entityManager = $this->entityManager;
        $benchmarkRepository = $entityManager->getRepository(BenchmarkResult::class);

        $output->writeln(sprintf('<info>parse paths for file: %s</info>', $sourceFile));

        $checkExist = strtolower($input->getArgument('checkExist')) === 'yes';



    }

}