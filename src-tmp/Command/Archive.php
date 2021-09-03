<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Archive extends Command
{
    protected function configure()
    {
        $this->setName('report:archive');
        $this->addArgument(
            'report',
            InputArgument::REQUIRED,
            'Set reportId'
        );
    }

    /**
     * todo save reports to zip archive
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reportFolderName = $input->getArgument('report');
        $basePath = realpath(__DIR__ . '/../../');
        $reportPath = 'data' . DIRECTORY_SEPARATOR . $reportFolderName;
        $compareDetailPath = $reportPath . DIRECTORY_SEPARATOR . 'compare-detail.log';
        $totalPath = $reportPath . DIRECTORY_SEPARATOR . 'total.log';

        $itemsCount = 327099;
        $totalPages = ceil($itemsCount / self::PER_PAGE);

        $useragents = [];


        $getDetailDir = $this->getDirDetailUa();

        $fn = fopen($compareDetailPath, 'a+');
        $html = [];
        $i = 0;
        $page = ceil($i / self::PER_PAGE) > 0 ?: 1;

        while (!feof($fn)) {
            $line = fgets($fn);
            if (empty($line)) {
                continue;
            }
            $i++;
            $newPage = ceil($i / self::PER_PAGE);
            $newPage = $newPage === 0 ? 1 : $newPage;

            $data = json_decode($line, true);
            $id = $data['id'];
            $useragent = $data['user_agent'];
            unset($data['user_agent']);

            $html[] = sprintf(
                '<li id="ua-%s"><a href="#ua-%s">%s</a> %s<div class="output" data-json="%s"></div></li>',
                $id,
                $id,
                $id,
                $useragent,
                json_encode($data)
            );

            if ($newPage !== $page) {
                $fileDetail = sprintf('%s/page-%s.html', $getDetailDir, $page);
                $pageContent = implode('', $html);
                $this->generatePage(
                    $pageContent,
                    $page,
                    $totalPages,
                    $fileDetail
                );
                $page = $newPage;
                $html = [];
            }
        }
        fclose($fn);
    }

    private const PER_PAGE = 2000;


    private function getBaseDir()
    {
        return __DIR__ . '/../../site';
    }

    private function getDirDetailUa()
    {
        $dir = $this->getBaseDir() . '/static/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }


    private function generatePage($pageContent, $page, $totalPage, $filename)
    {
        $html = $this->getHeader();
        $html .= '<ul>' . $pageContent . '</ul>';

        $html .= $this->getFooter();
        file_put_contents($filename, $html);
    }

    private function getPagination($currentPage, $totalPages)
    {

    }

    private function getHeader()
    {
        return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<title>benchmark-useragent-parser</title>
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes, minimum-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="/site/js/app.js" type="module"></script> 
</head><body>';
    }

    private function getFooter()
    {
        return '</body></html>';
    }


}